<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Color\Controller\Adminhtml\Color;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Designnbuy\Color\Controller\Adminhtml\Color
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
                'title' => __('Color Title'),
                'color_code' => __('Color Code'),
                'cmyk_color_code' => __('SPOT Color Code'),
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
        $template = '"{{title}}","{{color_code}}","{{cmyk_color_code}}"';
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
        $tableName = $resource->getTableName('designnbuy_color_color_label');

        $sql = $connection->select()->from(
            $tableName
        );

        $colorStoreLabels = $connection->fetchAll($sql);
        $colorStoreTitleDict = [];
        foreach ($colorStoreLabels as $title) {
            $colorId = $title['color_id'];
            if (!array_key_exists($colorId, $colorStoreTitleDict)) {
                $colorStoreTitleDict[$colorId] = $storeTaxTitleTemplate;
            }
            $colorStoreTitleDict[$colorId]['title_' . $title['store_id']] = $title['label'];
        }


        $collection = $this->_objectManager->create(
            'Designnbuy\Color\Model\ResourceModel\Color\Collection'
        );
        $colorData = array();
        while ($color = $collection->fetchItem()) {
            if (array_key_exists($color->getId(), $colorStoreTitleDict)) {
                $color->addData($colorStoreTitleDict[$color->getId()]);
            } else {
                $color->addData($storeTaxTitleTemplate);
            }
            $content .= $color->toString($template) . "\n";
        }

        return $this->fileFactory->create('colors.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_Color::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Color::import_export'
        );

    }
}
