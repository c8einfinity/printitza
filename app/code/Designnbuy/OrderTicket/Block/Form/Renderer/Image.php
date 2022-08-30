<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * OrderTicket Item Form Renderer Block for select
 */
namespace Designnbuy\OrderTicket\Block\Form\Renderer;

class Image extends \Magento\Framework\View\Element\Template
{
    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl(
            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
        ) . \Designnbuy\OrderTicket\Model\Item::ITEM_IMAGE_URL;
        $file = $this->getValue();
        $url = $url . $file;
        return $url;
    }
}
