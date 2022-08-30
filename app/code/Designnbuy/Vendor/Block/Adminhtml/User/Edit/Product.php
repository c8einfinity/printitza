<?php

namespace Designnbuy\Vendor\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Product extends GenericButton implements ButtonProviderInterface
{ 
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if($this->vendorData->isVendor() && $this->getModelId() != "") {
            $data = [
                'label' => __('Product'),
                'class' => 'save primary',
                'style' => 'background-color:green',
                'on_click' => sprintf("location.href = '%s';", $this->getProductUrl()),
                'sort_order' => 70,
            ];
        }
        return $data;
    }


    public function getProductUrl()
    {
        return $this->getUrl('*/*/product', ['vendor_id' => $this->getModelId()]);
    }
}
