<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Designnbuy\ConfigArea\Controller\Adminhtml\ConfigArea
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
                'title' => __('Design Area Title'),
                'configarea_code' => __('Design Area Code'),
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
        $template = '"{{title}}","{{configarea_code}}"';
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
        $tableName = $resource->getTableName('designnbuy_configarea_configarea_label');

        $sql = $connection->select()->from(
            $tableName
        );

        $configareaStoreLabels = $connection->fetchAll($sql);
        $configareaStoreTitleDict = [];
        foreach ($configareaStoreLabels as $title) {
            $configareaId = $title['configarea_id'];
            if (!array_key_exists($configareaId, $configareaStoreTitleDict)) {
                $configareaStoreTitleDict[$configareaId] = $storeTaxTitleTemplate;
            }
            $configareaStoreTitleDict[$configareaId]['title_' . $title['store_id']] = $title['label'];
        }


        $collection = $this->_objectManager->create(
            'Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea\Collection'
        );
        $configareaData = array();
        while ($configarea = $collection->fetchItem()) {
            if (array_key_exists($configarea->getId(), $configareaStoreTitleDict)) {
                $configarea->addData($configareaStoreTitleDict[$configarea->getId()]);
            } else {
                $configarea->addData($storeTaxTitleTemplate);
            }
            $content .= $configarea->toString($template) . "\n";
        }

        return $this->fileFactory->create('configareas.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_ConfigArea::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_ConfigArea::import_export'
        );

    }
}
