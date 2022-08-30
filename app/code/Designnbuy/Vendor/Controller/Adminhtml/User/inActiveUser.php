<?php
namespace Designnbuy\Vendor\Controller\Adminhtml\User;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Security\Model\SecurityCookie;

class inActiveUser extends \Magento\Backend\App\Action
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
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_userFactory = $userFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $model = $this->_objectManager->create('Designnbuy\Vendor\Model\User');
            $adminUserModel = $this->_userFactory->create();
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Vendor User no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                return $resultRedirect->setPath('*/*/');
            } else {
                $accountType = $this->getRequest()->getParam('type');
                if($accountType == 'activate'):
                    $adminUserModel->load($model->getUserId());
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');          
                    $connection = $this->_resources->getConnection();
                    $sql = "Update " . $this->_resources->getTableName('admin_user') . " Set `is_active` = '1' where `user_id` = ".$model->getUserId();
                    $connection->query($sql);

                    $model->setStatus(self::ACTIVE);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You activate this vendor successfully.'));
                else:
                    $adminUserModel->load($model->getUserId());
                    $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');          
                    $connection = $this->_resources->getConnection();
                    $sql = "Update " . $this->_resources->getTableName('admin_user') . " Set `is_active` = '0' where `user_id` = ".$model->getUserId();
                    $connection->query($sql);

                    $model->setStatus(self::INACTIVE);
                    $model->save();
                    $this->messageManager->addSuccessMessage(__('You inactive this vendor successfully.'));
                endif;
            }
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
