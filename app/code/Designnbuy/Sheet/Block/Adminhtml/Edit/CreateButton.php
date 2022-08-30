<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * Please visit Designnbuy.com for license details (https://designnbuy.com/end-user-license-agreement).
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Button "Create"
 */
class CreateButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Create'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 10
        ];
    }
}
