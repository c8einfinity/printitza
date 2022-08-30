<?php
namespace Designnbuy\Customer\Plugin\Group;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;


class Save {

    protected $_filterBuilder;
    protected $_groupFactory;
    protected $_groupRepository;
    protected $_searchCriteriaBuilder;
    protected $_group;

    public function __construct(
        FilterBuilder $filterBuilder,
        GroupRepositoryInterface $groupRepository, 
        SearchCriteriaBuilder $searchCriteriaBuilder, 
        GroupFactory $groupFactory, 
        \Designnbuy\Customer\Model\GroupFactory $group
    ) {
        $this->_filterBuilder = $filterBuilder;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_groupFactory = $groupFactory;
        $this->_group = $group;
    }       

    public function afterExecute(\Magento\Customer\Controller\Adminhtml\Group\Save $save, $result)
    {   
        $code = $save->getRequest()->getParam('code');
        
        $products = $save->getRequest()->getParam('products');
        $designideas = $save->getRequest()->getParam('designideas');
        $templates = $save->getRequest()->getParam('templates');
        //echo "<pre>"; print_r($save->getRequest()->getParams()); exit;
        if(empty($code))
            $code = 'NOT LOGGED IN';

        if($code){

            $_filter = [ $this->_filterBuilder->setField('customer_group_code')->setConditionType('eq')->setValue($code)->create() ];
            
            $customerGroups = $this->_groupRepository->getList($this->_searchCriteriaBuilder->addFilters($_filter)->create())->getItems();
            if(!empty($customerGroups)){
                $customerGroup = array_shift($customerGroups);
                if(!empty($customerGroup)){
                
                    try {
                        $groupInfo = $this->_group->create();
                        $groupInfo->load($customerGroup->getId());
                        
                        if(empty($groupInfo->getData())){
                            $groupInfo = $this->_group->create();
                        }
                        $groupInfo->setCustomerGroupId($customerGroup->getId());
                        /* echo "<pre>"; print_r($products).'<br/>';
                        echo "<pre>"; print_r($templates).'<br/>';
                        echo "<pre>"; print_r($designideas); exit; */
                        if(isset($products)){
                            $groupInfo->setProducts($products);
                        }
                        if(isset($templates)){
                            $groupInfo->setTemplates($templates);
                        }
                        if(isset($designideas)){
                            $groupInfo->setDesignidea($designideas);
                        }
                        
                        
                        $groupInfo->save();
                        //echo "<pre>"; print_r($groupInfo->getData()); exit;
                    } catch (\Exception $e) {
                        //echo $e->getMessage(); exit;
                    }
                }
            }
        }
        
        if ($save->getRequest()->getParam('back') == 'edit') {
            $result->setPath('designnbuy_customer/group/edit', ['id' => $customerGroup->getId()]);
        } else {
            $result->setPath('customer/group');
        }
        
        return $result;
    }       
}
?>