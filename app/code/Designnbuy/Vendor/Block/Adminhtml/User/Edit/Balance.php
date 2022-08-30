<?php

namespace Designnbuy\Vendor\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class Balance extends GenericButton implements ButtonProviderInterface
{ 
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if($this->vendorData->isVendor() && $this->getModelId() != "")
        {
            $data = [
                'label' => __('Balance Sheet'),
                'class' => 'save primary',
                'style' => 'background-color:green',
                'on_click' => sprintf("location.href = '%s';", $this->getBalanceSheetUrl()),
                'sort_order' => 70,
            ];
        }
        return $data;
    }


    public function getBalanceSheetUrl()
    {
        return $this->getUrl('*/transaction/index', ['vendor_id' => $this->getModelId()]);
    }
}
