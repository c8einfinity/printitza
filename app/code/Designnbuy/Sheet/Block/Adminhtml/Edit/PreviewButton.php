<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * Please visit Designnbuy.com for license details (https://designnbuy.com/end-user-license-agreement).
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class PreviewButton
 */
class PreviewButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->getObjectId()) {
            $data = [
                'label' => __('Preview'),
                'class' => 'preview',
                'on_click' => 'window.open(\'' . $this->getPreviewUrl() . '\');',
                'sort_order' => 35,
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getPreviewUrl()
    {
        return $this->getUrl('*/*/preview', ['id' => $this->getObjectId()]);
    }
}
