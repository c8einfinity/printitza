<?php

namespace Designnbuy\Vendor\Controller\Adminhtml\User;

/**
 * Designer Designer change status controller
 */

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory;

class MassStatus extends \Magento\Backend\App\Action
{

	const ACTIVE = 1;

    const INACTIVE = 0;

	/**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var VendorUser
     */
    protected $_vendorUser;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
    	Context $context, 
    	Filter $filter, 
    	CollectionFactory $collectionFactory,
    	\Designnbuy\Vendor\Model\User $vendorUser,
    	\Magento\User\Model\UserFactory $userFactory
    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->_vendorUser = $vendorUser;
        $this->_userFactory = $userFactory;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        $status = $this->getRequest()->getParam('status');
        foreach ($collection as $user) {
        	$status = ($status == 1) ? self::ACTIVE : self::INACTIVE;

        	$userId = $user->getId();
        	$vendor = $this->_vendorUser->load($userId);
        	$vendor->setStatus($status);
        	$userId = $vendor->getUserId();
        	$vendor->save();
        	$this->changeUserStatus($userId, $status);
        }
        $this->messageManager->addSuccess(__('A total of %1 record(s) have been updated.', $collectionSize));
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    public function changeUserStatus($userId, $status) {

       	$data = ['is_active' => $status];
       	
    	$this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
    	
    	$this->_resources->getConnection()->update(
    		'admin_user',
    		$data,
		    ['user_id = ?' => (int)$userId]
		);
    }
}
