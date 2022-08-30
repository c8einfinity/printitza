<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Clipart\Model\Clipart;

/**
 * Clipart CSV Import Handler
 */
class CsvImportHandler
{
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;

    /**
     * Clipart factory
     *
     * @var \Designnbuy\Clipart\Model\ClipartFactory
     */
    protected $_clipartFactory;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Designnbuy\Clipart\Model\ClipartFactory $clipartFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Designnbuy\Clipart\Model\ClipartFactory $clipartFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        // prevent admin store from loading
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_clipartFactory = $clipartFactory;
        $this->csvProcessor = $csvProcessor;
    }

    /**
     * Retrieve a list of fields required for CSV file (order is important!)
     *
     * @return array
     */
    public function getRequiredCsvFields()
    {
        // indexes are specified for clarity, they are used during import
        return [
            0 => __('Title'),
            1 => __('Image')
        ];
    }

    /**
     * Import Cliparts from CSV file
     *
     * @param array $file file info retrieved from $_FILES array
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function importFromCsvFile($file)
    {
        if (!isset($file['tmp_name'])) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
        }
        $clipartRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $clipartRawData[0];
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $clipartsData = $this->_filterClipartData($clipartRawData, $invalidFields, $validFields);
        // store cache array is used to quickly retrieve store ID when handling locale-specific clipart titles
        $storesCache = $this->_composeStoreCache($validFields);
        foreach ($clipartsData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $this->_importClipart($dataRow, $storesCache);
        }
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {
        $filteredFields = $this->getRequiredCsvFields();
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $fileFieldsNum = count($fileFields);

        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            if ($this->_isStoreCodeValid($titleFieldName)) {
                // if store is still valid, append this field to valid file fields
                $filteredFields[$index] = $titleFieldName;
            }
        }

        return $filteredFields;
    }

    /**
     * Filter cliparts data (i.e. unset all invalid fields and check consistency)
     *
     * @param array $clipartRawData
     * @param array $invalidFields assoc array of invalid file fields
     * @param array $validFields assoc array of valid file fields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _filterClipartData(array $clipartRawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($clipartRawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($clipartRawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($clipartRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($clipartRawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $clipartRawData;
    }

    /**
     * Compose stores cache
     *
     * This cache is used to quickly retrieve store ID when handling locale-specific clipart titles
     *
     * @param string[] $validFields list of valid CSV file fields
     * @return array
     */
    protected function _composeStoreCache($validFields)
    {
        $storesCache = [];
        $requiredFieldsNum = count($this->getRequiredCsvFields());
        $validFieldsNum = count($validFields);
        // title related fields located right after required fields
        for ($index = $requiredFieldsNum; $index < $validFieldsNum; $index++) {
            foreach ($this->_publicStores as $store) {
                $storeCode = $validFields[$index];
                if ($storeCode === $store->getCode()) {
                    $storesCache[$index] = $store->getId();
                }
            }
        }
        return $storesCache;
    }

    /**
     * Check if public store with specified code still exists
     *
     * @param string $storeCode
     * @return boolean
     */
    protected function _isStoreCodeValid($storeCode)
    {
        $isStoreCodeValid = false;
        foreach ($this->_publicStores as $store) {
            if ($storeCode === $store->getCode()) {
                $isStoreCodeValid = true;
                break;
            }
        }
        return $isStoreCodeValid;
    }

    /**
     * Import single clipart
     *
     * @param array $clipartData
     * @param array $storesCache cache of stores related to clipart titles
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importClipart(array $clipartData, array $storesCache)
    {
        $modelData = [
            'title' => $clipartData[0],
            'image' => $clipartData[1]
        ];

        /** @var $clipartModel \Designnbuy\Clipart\Model\Clipart */
        $clipartModel = $this->_clipartFactory->create();
        $clipartModel->addData($modelData);

        // compose titles list
        $storeTitles = [];
        foreach ($storesCache as $fileFieldIndex => $storeId) {
            $storeTitles[$storeId] = $clipartData[$fileFieldIndex];
        }
        $clipartModel->setStoreLabels($storeTitles);
        $clipartModel->save();
    }
}
