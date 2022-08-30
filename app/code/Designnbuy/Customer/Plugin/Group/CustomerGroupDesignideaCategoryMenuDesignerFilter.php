<?php

namespace Designnbuy\Customer\Plugin\Group;

use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;

class CustomerGroupDesignideaCategoryMenuDesignerFilter
{
    protected $session;

    protected $_group;

    /**
     * Product collection factory
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $designideaCollectionFactory;

    public function __construct(
        Session $customerSession,
        GroupFactory $groupFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
    ) {
        $this->session = $customerSession;
        $this->_group = $groupFactory;
        $this->designIdeaCollectionFactory = $designideaCollectionFactory;
    }

	public function afterMenuFilter(\Designnbuy\Designidea\Helper\Menu $subject, $result, $item)
	{
        if ($this->session->isLoggedIn()) {
            
            if($this->session->getCustomer()->getGroupId()){
                
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getDesignidea()){
                    $groupDesignideas = explode("&",$groupInfo->getDesignidea());
                    if(!empty($groupDesignideas)){
                        
                        $posts = $this->designIdeaCollectionFactory->create()
                        ->addActiveFilter()
                        //->addStoreFilter($subject->getStore())
                        ->addCategoryFilter($item);
                    
                        $posts->addAttributeToFilter(
                            'entity_id',
                            ['in' => $groupDesignideas]
                        );
                        if(!$posts->getSize()){
                            return true;
                        }
                    }
                }

            }

        } else {
            
            $groupInfo = $this->_group->create();
            $groupInfo->load(0);
            
            if($groupInfo->getDesignidea()){
                $groupDesignideas = explode("&",$groupInfo->getDesignidea());
                if(!empty($groupDesignideas)){
                    
                    $posts = $this->designIdeaCollectionFactory->create()
                    ->addActiveFilter()
                    //->addStoreFilter($subject->getStore())
                    ->addCategoryFilter($item);
                
                    $posts->addAttributeToFilter(
                        'entity_id',
                        ['nin' => $groupDesignideas]
                    );
                    if(!$posts->getSize()){
                        return true;
                    }
                }
            }
        }
        
        return false;
	}

}