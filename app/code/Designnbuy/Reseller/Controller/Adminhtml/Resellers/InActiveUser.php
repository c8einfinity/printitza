<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Security\Model\SecurityCookie;

class InActiveUser extends \Magento\Backend\App\Action
{
    protected $dataPersistor;

    /**
     * User model factory
     *
     * @var \Magento\User\Model\UserFactory
     */
    protected $_userFactory;

    const ACTIVE = 1;

    const INACTIVE = 0;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Magento\User\Model\UserFactory $userFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Reseller\Model\Resellers $reseller
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        $this->_reseller = $reseller;
        parent::__construct($context);
    }

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');
            $resellerModel = $this->_reseller->load($id);
            $adminUserModel = $this->_userFactory->create();

            if (!$resellerModel->getResellerId()) {
                $this->messageManager->addErrorMessage(__('This Reseller no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                return $this->_redirect('*/*/');
            } else {
                $accountType = $this->getRequest()->getParam('type');
                if($accountType == 'activate'):
                    $adminUserModel->load($resellerModel->getUserId());
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');          
                    $connection = $this->_resources->getConnection();
                    $sql = "Update " . $this->_resources->getTableName('admin_user') . " Set `is_active` = '1' where `user_id` = ".$resellerModel->getUserId();
                    $connection->query($sql);

                    $resellerModel->setStatus(self::ACTIVE);
                    $resellerModel->save();
                    $this->messageManager->addSuccessMessage(__('You activate this reseller successfully.'));
                else:
                    $adminUserModel->load($resellerModel->getUserId());
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');          
                    $connection = $this->_resources->getConnection();
                    $sql = "Update " . $this->_resources->getTableName('admin_user') . " Set `is_active` = '0' where `user_id` = ".$resellerModel->getUserId();
                    $connection->query($sql);

                    $resellerModel->setStatus(self::INACTIVE);
                    $resellerModel->save();
                    $this->messageManager->addSuccess(__('You inactive this reseller successfully.'));
                endif;
            }
            return $this->_redirect('*/*/edit', ['reseller_id' => $this->getRequest()->getParam('id')]);
            
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
