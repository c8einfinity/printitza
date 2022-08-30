<?php

namespace Designnbuy\Commission\Block\Adminhtml\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class SaveButton
 */
class SaveButton extends GenericButton implements ButtonProviderInterface
{
    public function __construct(
        \Designnbuy\Commission\Helper\Data $commissionHelper
    ) {
       $this->commissionHelper = $commissionHelper;
    }
    /**
     * @return array
     */
    public function getButtonData()
    {
    $isWebsiteOwner = $this->commissionHelper->isWebsiteOwner();
    if($isWebsiteOwner != true):
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    endif;
    }
}
