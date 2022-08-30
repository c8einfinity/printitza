<?php


namespace Designnbuy\Vendor\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        if(!$this->vendorData->isVendor()) {
            return [
                'label' => __('Back'),
                'on_click' => sprintf("location.href = '%s';", $this->getBackUrl()),
                'class' => 'back',
                'sort_order' => 10
            ];
        }
        return;
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
