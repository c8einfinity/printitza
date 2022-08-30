<?php
/**
 * @author Drc Systems India Pvt Ltd.
 */

namespace Drc\Storepickup\Controller\Adminhtml\Import;

class Save extends \Drc\Storepickup\Controller\Adminhtml\Import
{

    public function execute()
    {
        try {
            $uploader = $this->_objectManager->create('Magento\MediaStorage\Model\File\Uploader', ['fileId' => 'import_storelocations_file']);
            $Files = $uploader->validateFile();
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('drc_storepickup/*/');
            return;
        }

        if ($this->getRequest()->isPost() && !empty($Files['tmp_name'])) {
            try {
                $objectManager      = \Magento\Framework\App\ObjectManager::getInstance();

                if (!isset($Files['tmp_name'])) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file upload attempt.'));
                }

                $CsvObject              =   $objectManager->get('Magento\Framework\File\Csv');
                $storeLocatorRawData    =   $CsvObject->getData($Files['tmp_name']);
                // first row of file represents headers

                $fileFields     = $storeLocatorRawData[0];
                $validFields    = $this->_filterFileFields($fileFields);
                $invalidFields  = array_diff_key($fileFields, $validFields);
                $locatorsData   = $this->_filterStoreLocatorData($storeLocatorRawData, $invalidFields, $validFields);

                $regionsCache = [];

                foreach ($locatorsData as $rowIndex => $dataRow) {
                    // skip headers
                    if ($rowIndex == 0) {
                        continue;
                    }
                    $regionsCache = $this->_importStoreData($dataRow);
                }

                $this->messageManager->addSuccess(__('The Storelocator has been imported.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Invalid file upload attempt'));
            }
        } else {
            $this->messageManager->addError(__('Invalid file upload attempt'));
        }

        $this->_redirect('drc_storepickup/*/');
    }

    /**
     * Filter file fields (i.e. unset invalid fields)
     *
     * @param array $fileFields
     * @return string[] filtered fields
     */
    protected function _filterFileFields(array $fileFields)
    {

        $filteredFields     = $this->getRequiredCsvFields();
        $requiredFieldsNum  = count($this->getRequiredCsvFields());
        $fileFieldsNum      = count($fileFields);

        // process title-related fields that are located right after required fields with store code as field name)
        for ($index = $requiredFieldsNum; $index < $fileFieldsNum; $index++) {
            $titleFieldName = $fileFields[$index];
            //if ($this->_isStoreCodeValid($titleFieldName)) {
            // if store is still valid, append this field to valid file fields
            $filteredFields[$index] = $titleFieldName;
            //}
        }
        return $filteredFields;
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
            0 => __('Store Title'),
            1 => __('Address'),
            2 => __('City'),
            3 => __('State'),
            4 => __('Pin Code'),
            5 => __('Country'),
            6 => __('Phone Number'),
            7 => __('Email'),
            8 => __('Status'),
            9 => __('Latitude'),
            10 => __('Longitude'),
        ];
    }

    protected function _filterStoreLocatorData(array $storeLocatorData, array $invalidFields, array $validFields)
    {
        $validFieldsNum = count($validFields);
        foreach ($storeLocatorData as $rowIndex => $dataRow) {
            // skip empty rows
            if (count($dataRow) <= 1) {
                unset($storeLocatorData[$rowIndex]);
                continue;
            }
            // unset invalid fields from data row
            foreach ($dataRow as $fieldIndex => $fieldValue) {
                if (isset($invalidFields[$fieldIndex])) {
                    unset($storeLocatorData[$rowIndex][$fieldIndex]);
                }
            }
            // check if number of fields in row match with number of valid fields
            if (count($storeLocatorData[$rowIndex]) != $validFieldsNum) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid file format.'));
            }
        }
        return $storeLocatorData;
    }

    protected function _importStoreData(array $storeData)
    {
        // data with index 1 must represent country code
        $countryCode = $storeData[6];

        $objectManager      = \Magento\Framework\App\ObjectManager::getInstance();
        $countryObject      =   $objectManager->get('\Magento\Directory\Model\CountryFactory');

        $country = $countryObject->create()->loadByCode($countryCode, 'iso2_code');
        if (!$country->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('One of the countries has invalid code.'));
        }

        // data with index 2 must represent region code
        $modelData = [
            'store_title' => $storeData[1],
            'address'     => $storeData[2],
            'city'        => $storeData[3],
            'state'       => $storeData[4],
            'pincode'     => $storeData[5],
            'country'     => $storeData[6],
            'phone'       => $storeData[7],
            'email'       => $storeData[8],
            'is_enable'   => $storeData[9],
            'latitude'    => $storeData[10],
            'longitude'   => $storeData[11],
        ];

        $objectManager          = \Magento\Framework\App\ObjectManager::getInstance();
        $storeCollection        = $objectManager->create('Drc\Storepickup\Model\Storelocator');

        $storeCollection->setData($modelData);
        $storeCollection->save();
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
}
