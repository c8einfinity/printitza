<?php

namespace Designnbuy\Customer\Plugin\Group;

use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;

class CustomerGroupTemplateCategoryDesignerFilter
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

	public function aroundGetTemplatesCount(\Designnbuy\Template\Model\Category $subject, callable $proceed)
	{
        if ($this->session->isLoggedIn()) {
            
            if($this->session->getCustomer()->getGroupId()){
                
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getTemplates()){
                    $groupTemplates = explode("&",$groupInfo->getTemplates());
                    if(!empty($groupTemplates)){
                        
                        $key = 'templates_count';
        
                        $posts = $this->templateCollectionFactory->create()
                            ->addActiveFilter()
                            //->addStoreFilter($subject->getStore())
                            ->addCategoryFilter($subject);
                        
                        $posts->addAttributeToFilter(
                            'entity_id',
                            ['in' => $groupTemplates]
                        );

                        return (int)$posts->getSize();
                    }
                }

            }

        } else {
            $groupInfo = $this->_group->create();
            $groupInfo->load(0);

            if($groupInfo->getTemplates()){
                $groupTemplates = explode("&",$groupInfo->getTemplates());
                if(!empty($groupTemplates)){
                    
                    $key = 'templates_count';
    
                    $posts = $this->templateCollectionFactory->create()
                        ->addActiveFilter()
                        //->addStoreFilter($subject->getStore())
                        ->addCategoryFilter($subject);
                    
                    $posts->addAttributeToFilter(
                        'entity_id',
                        ['nin' => $groupTemplates]
                    );

                    return (int)$posts->getSize();
                }
            }
        }
        
        $result = $proceed();
        return $result;
        
	}

}