<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Block\Adminhtml\Transaction\Grid\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
/**
 * Class Back
 */
class Back extends Generic implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/user/',['vendor_id' => $this->getVendorId()])),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
