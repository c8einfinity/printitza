<?php

namespace Designnbuy\Customer\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;

class CustomerGroupProductFilter implements ObserverInterface
{
    protected $session;

    protected $_group;

    public function __construct(
        Session $customerSession,
        GroupFactory $groupFactory
    ) {
        $this->session = $customerSession;
        $this->_group = $groupFactory;
    }

    /**
     * customer register event handler
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $productCollection = $observer->getData('collection');
        if ($this->session->isLoggedIn()) {
            
            if($this->session->getCustomer()->getGroupId()){
                
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getProducts()){
                    $groupProd = explode("&",$groupInfo->getProducts());
                    if(!empty($groupProd)){
                        $productCollection->addAttributeToFilter(
                            'entity_id',
                            ['in' => $groupProd]
                        );
                    }
                }

            }

        } else {
            $groupInfo = $this->_group->create();
            $groupInfo->load(0);
                if($groupInfo->getProducts()){
                    $groupProd = explode("&",$groupInfo->getProducts());
                    if(!empty($groupProd)){
                        $productCollection->addAttributeToFilter(
                            'entity_id',
                            ['nin' => $groupProd]
                        );
                    }
                }
        }
        
    }
}