<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Font\Controller\Adminhtml\Font;

use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class ExportPost extends \Designnbuy\Font\Controller\Adminhtml\Font
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
                'woff' => __('Woff'),
                'js' => __('JS'),
                'ttf' => __('Ttf'),
                'ttfbold' => __('Ttf Bold'),
                'ttfitalic' => __('Ttf Italic'),
                'ttfbolditalic' => __('Ttf Bold Italic')
            ]
        );
        
        $template = '"{{title}}","{{woff}}","{{js}}","{{ttf}}","{{ttfbold}}","{{ttfitalic}}","{{ttfbolditalic}}"';
        $content = $headers->toString($template);
        $content .= "\n";

        $collection = $this->_objectManager->create(
            'Designnbuy\Font\Model\ResourceModel\Font\Collection'
        );

        while ($font = $collection->fetchItem()) {
            $content .= $font->toString($template) . "\n";
        }

        return $this->fileFactory->create('fonts.csv', $content, DirectoryList::VAR_DIR);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed(
            'Designnbuy_Font::elements'
        ) || $this->_authorization->isAllowed(
            'Designnbuy_Font::import_export'
        );

    }
}
