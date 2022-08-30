<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Color\Model\Color;

/**
 * Color CSV Import Handler
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
     * Color factory
     *
     * @var \Designnbuy\Color\Model\ColorFactory
     */
    protected $_colorFactory;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     * @param \Designnbuy\Color\Model\ColorFactory $colorFactory
     * @param \Magento\Framework\File\Csv $csvProcessor
     */
    public function __construct(
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Designnbuy\Color\Model\ColorFactory $colorFactory,
        \Magento\Framework\File\Csv $csvProcessor
    ) {
        // prevent admin store from loading
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_colorFactory = $colorFactory;
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
            0 => __('Color Title'),
            1 => __('Color Code')
        ];
    }

    /**
     * Import Colors from CSV file
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
        $colorRawData = $this->csvProcessor->getData($file['tmp_name']);
        // first row of file represents headers
        $fileFields = $colorRawData[0];
        $validFields = $this->_filterFileFields($fileFields);
        $invalidFields = array_diff_key($fileFields, $validFields);
        $colorsData = $this->_filterColorData($colorRawData, $invalidFields, $validFields);
        // store cache array is used to quickly retrieve store ID when handling locale-specific color titles
        $storesCache = $this->_composeStoreCache($validFields);
        foreach ($colorsData as $rowIndex => $dataRow) {
            // skip headers
            if ($rowIndex == 0) {
                continue;
            }
            $this->_importColor($dataRow, $storesCache);
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
     * Filter colors data (i.e. unset all invalid fields and check consistency)
     *
     * @param array $colorRawData
     * @param array $invalidFields assoc array of invalid file fields
     * @param array $validFields assoc array of valid file fields
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function _filterColorData(array $colorRawData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($colorRawData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($colorRawData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($colorRawData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($colorRawData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $colorRawData;
    }

    /**
     * Compose stores cache
     *
     * This cache is used to quickly retrieve store ID when handling locale-specific color titles
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
     * Import single color
     *
     * @param array $colorData
     * @param array $storesCache cache of stores related to color titles
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _importColor(array $colorData, array $storesCache)
    {
        $modelData = [
            'title' => $colorData[0],
            'color_code' => $colorData[1]
        ];

        /** @var $colorModel \Designnbuy\Color\Model\Color */
        $colorModel = $this->_colorFactory->create();
        $colorModel->addData($modelData);

        // compose titles list
        $storeTitles = [];
        foreach ($storesCache as $fileFieldIndex => $storeId) {
            $storeTitles[$storeId] = $colorData[$fileFieldIndex];
        }
        $colorModel->setStoreLabels($storeTitles);
        $colorModel->save();
    }
}
