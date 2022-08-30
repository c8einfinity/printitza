<?php
namespace Designnbuy\Orderattachment\Block\Cart\Item\Renderer\Actions;
use Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic;
use Magento\Backend\Block\Template\Context;
class Preview extends Generic
{

    public function __construct(
        Context $context,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Orderattachment\Helper\Data $orderAttchmentHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->cart = $cart;
        $this->_orderAttchmentHelper = $orderAttchmentHelper;
    }

    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    public function getAttachments()
    {
        $_item = $this->getItem();
        $itemAttachments = [];
        if($_item){
            $infoBuyRequest = $_item->getBuyRequest()->getData();
            if(isset($infoBuyRequest) && isset($infoBuyRequest['attachment'])){
                $itemAttachments = $infoBuyRequest['attachment'];
            }
        }
        return $itemAttachments;
    }

    public function getImageUrlPath()
    {
        return $this->_orderAttchmentHelper->getImageUrlPath();
    }

}
