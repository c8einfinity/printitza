<?php

namespace Designnbuy\Customer\Plugin\Group;

use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;

class CustomerGroupTemplateCategoryMenuDesignerFilter
{
    protected $session;

    protected $_group;

    /**
     * Product collection factory
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;

    public function __construct(
        Session $customerSession,
        GroupFactory $groupFactory,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    ) {
        $this->session = $customerSession;
        $this->_group = $groupFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

	public function afterMenuFilter(\Designnbuy\Template\Helper\Menu $subject, $result, $item)
	{
        if ($this->session->isLoggedIn()) {
            
            if($this->session->getCustomer()->getGroupId()){
                
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getTemplates()){
                    $groupTemplates = explode("&",$groupInfo->getTemplates());
                    if(!empty($groupTemplates)){
                        
                        $posts = $this->templateCollectionFactory->create()
                        ->addActiveFilter()
                        //->addStoreFilter($subject->getStore())
                        ->addCategoryFilter($item);
                    
                        $posts->addAttributeToFilter(
                            'entity_id',
                            ['in' => $groupTemplates]
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

            if($groupInfo->getTemplates()){
                $groupTemplates = explode("&",$groupInfo->getTemplates());
                if(!empty($groupTemplates)){
                    
                    $posts = $this->templateCollectionFactory->create()
                    ->addActiveFilter()
                    //->addStoreFilter($subject->getStore())
                    ->addCategoryFilter($item);
                
                    $posts->addAttributeToFilter(
                        'entity_id',
                        ['nin' => $groupTemplates]
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