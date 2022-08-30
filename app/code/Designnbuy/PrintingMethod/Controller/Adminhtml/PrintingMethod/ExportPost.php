<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\PrintingMethod\Controller\Adminhtml\PrintingMethod;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Designnbuy\PrintingMethod\Controller\Adminhtml\PrintingMethod
{
    /**
     * Export action from import/export tax
     *
     * @return ResponseInterface
     */
    public function execute()
    {
        /** start csv content and set template */
        $headers = new \Magento\Framework\DataObject(
            [
                'title' => __('PrintingMethod Title'),
                'printingmethod_code' => __('PrintingMethod Code'),
               // 'categories' => __('Category'),
                /*'tax_postcode' => __('Zip/Post Code'),
                'rate' => __('Rate'),
                'zip_is_range' => __('Zip/Post is Range'),
                'zip_from' => __('Range From'),
                'zip_to' => __('Range To'),*/
            ]
        );
        /*$template = '"{{code}}","{{country_name}}","{{region_name}}","{{tax_postcode}}","{{rate}}"' .
            ',"{{zip_is_range}}","{{zip_from}}","{{zip_to}}"';*/
        $template = '"{{title}}","{{printingmethod_code}}"';
        $content = $headers->toString($template);

        $storeTaxTitleTemplate = [];


        foreach ($this->_objectManager->create(
            'Magento\Store\Model\Store'
        )->getCollection()->setLoadDefault(
            false
        ) as $store) {
            $storeTitle = 'title_' . $store->getId();
            $content .= ',"' . $store->getCode() . '"';
            $template .= ',"{{' . $storeTitle . '}}"';
            $storeTaxTitleTemplate[$storeTitle] = null;
        }
        unset($store);

        $content .= "\n";
        $resource = $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();
        $tableName = $resource->getTableName('designnbuy_printingmethod_printingmethod_label');

        $sql = $connection->select()->from(
            $tableName
        );

        $printingmethodStoreLabels = $connection->fetchAll($sql);
        $printingmethodStoreTitleDict = [];
        foreach ($printingmethodStoreLabels as $title) {
            $printingmethodId = $title['printingmethod_id'];
            if (!array_key_exists($printingmethodId, $printingmethodStoreTitleDict)) {
                $printingmethodStoreTitleDict[$printingmethodId] = $storeTaxTitleTemplate;
            }
            $printingmethodStoreTitleDict[$printingmethodId]['title_' . $title['store_id']] = $title['label'];
        }


        $collection = $this->_objectManager->create(
            'Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection'
        );
        $printingmethodData = array();
        while ($printingmethod = $collection->fetchItem()) {
            if (array_key_exists($printingmethod->getId(), $printingmethodStoreTitleDict)) {
                $printingmethod->addData($printingmethodStoreTitleDict[$printingmethod->getId()]);
            } else {
                $printingmethod->addData($storeTaxTitleTemplate);
            }
            $content .= $printingmethod->toString($template) . "\n";
        }

        return $this->fileFactory->create('printingmethods.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_PrintingMethod::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_PrintingMethod::import_export'
        );

    }
}
