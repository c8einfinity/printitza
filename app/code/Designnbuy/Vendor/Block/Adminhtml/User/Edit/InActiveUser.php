<?php

namespace Designnbuy\Vendor\Block\Adminhtml\User\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class InActiveUser extends GenericButton implements ButtonProviderInterface
{ 
    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];
        if(!$this->vendorData->isVendor() && $this->getModelId() != "") 
        {
            $vendor = $this->getVendor();
            if($vendor && $vendor->getStatus() == false):
                $data = [
                    'label' => __('Active User'),
                    'class' => 'save primary',
                    'style' => 'background-color:green',
                    'on_click' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to active this User?'
                        ) . '\', \'' . $this->getActiveUserUrl() . '\')',
                    'sort_order' => 70,
                ];
            else:
                $data = [
                    'label' => __('In Active User'),
                    'class' => 'save primary',
                    'style' => 'background-color:red',
                    'on_click' => 'deleteConfirm(\'' . __(
                            'Are you sure you want to inactive this User?'
                        ) . '\', \'' . $this->getInActiveUserUrl() . '\')',
                    'sort_order' => 70,
                ];
            endif;
        }
        return $data;
    }

    public function getInActiveUserUrl()
    {
        return $this->getUrl('*/*/InActiveUser', ['id' => $this->getModelId()]);
    }

    public function getActiveUserUrl()
    {
        return $this->getUrl('*/*/InActiveUser', ['id' => $this->getModelId(), 'type' => 'activate']);
    }
}
