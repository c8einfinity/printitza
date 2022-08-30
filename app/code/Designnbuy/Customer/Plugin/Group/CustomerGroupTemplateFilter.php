<?php

namespace Designnbuy\Customer\Plugin\Group;

use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;

class CustomerGroupTemplateFilter
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

	public function afterGetTemplateCollection(\Designnbuy\Template\Block\Template\TemplateList\AbstractList $subject, $result)
	{
        if ($this->session->isLoggedIn()) {
            
            if($this->session->getCustomer()->getGroupId()){
                
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getTemplates()){
                    $groupTemplates = explode("&",$groupInfo->getTemplates());
                    if(!empty($groupTemplates)){
                        
                        $result->addAttributeToFilter(
                            'entity_id',
                            ['in' => $groupTemplates]
                        );

                    }
                }

            }

        } else {
            $groupInfo = $this->_group->create();
            $groupInfo->load(0);
            
            if($groupInfo->getTemplates()){
                $groupTemplates = explode("&",$groupInfo->getTemplates());
                if(!empty($groupTemplates)){
                    
                    $result->addAttributeToFilter(
                        'entity_id',
                        ['nin' => $groupTemplates]
                    );

                }
            }
        }
        
        return $result;
	}

}