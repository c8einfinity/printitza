<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Block\Adminhtml\Transaction\Grid\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton
 */
class Add extends Generic implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if ($this->isSuperAdmin() && $this->getVendorId()) {
            $data = [
                'label' => __('Add Transaction'),
                'on_click' => sprintf("location.href = '%s';", $this->getAddUrl()),
                'class' => 'primary',
                'sort_order' => 10
            ];
        }
        return $data;
    }

    /**
     * @return string
     */
    public function getAddUrl()
    {
        return $this->getUrl('*/*/new', ['vendor_id' => $this->getVendorId()]);
    }
}
