<?php
namespace Designnbuy\Customer\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Controller\Adminhtml\Group;
use Magento\Customer\Api\GroupRepositoryInterface;
/**
 * Class Edit
 * @package Designnbuy\Productattach\Controller\Adminhtml\Index
 */
class NewAction extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;
    
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    private $groupRepositoryInterface;

    /**
     * @var  \Magento\Backend\Model\Session
     */
    private $backSession;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Api\GroupRepositoryInterface $GroupRepositoryInterface
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $registry;
        $this->groupRepositoryInterface = $GroupRepositoryInterface;
        $this->backSession = $context->getSession();
        parent::__construct($context);
    }

    /**
     * Init actions
     *
     * @return $this
     */
    protected function _initGroup()
    {
        $groupId = $this->getRequest()->getParam('id');
        $this->coreRegistry->register(RegistryConstants::CURRENT_GROUP_ID, $groupId);

        return $groupId;
    }

    /**
     * Edit CMS page
     *
     * @return void
     */
    public function execute()
    {

        $groupId = $this->_initGroup();
        
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Magento_Customer::customer_group');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Groups'));
        $resultPage->addBreadcrumb(__('Customers'), __('Customers'));
        $resultPage->addBreadcrumb(__('Customer Groups'), __('Customer Groups'), $this->getUrl('customer/group'));

        if ($groupId === null) {
            $resultPage->addBreadcrumb(__('New Group'), __('New Customer Groups'));
            $resultPage->getConfig()->getTitle()->prepend(__('New Customer Group'));
        } else {
            $resultPage->addBreadcrumb(__('Edit Group'), __('Edit Customer Groups'));
            $resultPage->getConfig()->getTitle()->prepend(
                $this->groupRepositoryInterface->getById($groupId)->getCode()
            );
        }

        return $resultPage;
    }
}
