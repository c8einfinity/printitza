<?php


namespace Designnbuy\Vendor\Block\Adminhtml\Transaction\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Transaction'),
            'class' => 'save primary',
            'on_click' => sprintf("location.href = '%s';", $this->getSaveUrl()),
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', ['vendor_id' => $this->getVendorId()]);
    }
}
