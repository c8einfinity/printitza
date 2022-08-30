<?php
namespace Designnbuy\Base\Plugin;

class RemoveAttributeDelete
{

    protected $baseHelper;

    public function __construct(
        \Designnbuy\Base\Helper\Data $baseHelper
    )
    {
        $this->baseHelper = $baseHelper;
    }

    public function afterGetIsCurrentSetDefault(\Magento\Catalog\Block\Adminhtml\Product\Attribute\Set\Main $subject, $isDefault)
    {
        $isDefault;

        if($subject->getRequest()->getParam('id') && !empty($this->baseHelper->getDefaultAttributeIds()))
        {
            if(in_array($subject->getRequest()->getParam('id'), $this->baseHelper->getDefaultAttributeIds())){
                $isDefault = 1;
            }
        }
        
        return $isDefault;
        
    }

}