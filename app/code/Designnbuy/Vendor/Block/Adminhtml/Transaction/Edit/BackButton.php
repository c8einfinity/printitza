<?php


namespace Designnbuy\Vendor\Block\Adminhtml\Transaction\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class BackButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getTransactionUrl()),
            'class' => 'back',
            'sort_order' => 10
        ];
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getTransactionUrl()
    {
        return $this->getUrl('designnbuy_vendor/transaction/index',['vendor_id' => $this->getVendorId()]);
    }
}
