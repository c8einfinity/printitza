<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Clipart\Controller\Adminhtml\Clipart;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Designnbuy\Clipart\Controller\Adminhtml\Clipart
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
                'title' => __('Title'),
                'image' => __('Image'),
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
        $template = '"{{title}}","{{image}}"';
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
        $tableName = $resource->getTableName('designnbuy_clipart_clipart_label');

        $sql = $connection->select()->from(
            $tableName
        );

        $clipartStoreLabels = $connection->fetchAll($sql);
        $clipartStoreTitleDict = [];
        foreach ($clipartStoreLabels as $title) {
            $clipartId = $title['clipart_id'];
            if (!array_key_exists($clipartId, $clipartStoreTitleDict)) {
                $clipartStoreTitleDict[$clipartId] = $storeTaxTitleTemplate;
            }
            $clipartStoreTitleDict[$clipartId]['title_' . $title['store_id']] = $title['label'];
        }


        $collection = $this->_objectManager->create(
            'Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection'
        );
        $clipartData = array();
        while ($clipart = $collection->fetchItem()) {

            if (array_key_exists($clipart->getId(), $clipartStoreTitleDict)) {
                $clipart->addData($clipartStoreTitleDict[$clipart->getId()]);
            } else {
                $clipart->addData($storeTaxTitleTemplate);
            }

            $content .= $clipart->toString($template) . "\n";
        }
        
        return $this->fileFactory->create('cliparts.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_Clipart::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Clipart::import_export'
        );

    }
}
