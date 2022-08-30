<?php

namespace Designnbuy\Commission\Controller\Commission;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Design home page view
 */
class Save extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Commission\Model\Redemption $redemptionFactory,
        \Designnbuy\Notifications\Model\Notifications $notificationFactory
    ) {
        parent::__construct($context);
        $this->redemptionFactory = $redemptionFactory;
        $this->notificationFactory = $notificationFactory;
    }

    public function execute()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerSession = $objectManager->get('Magento\Customer\Model\Session');
        if(!$customerSession->isLoggedIn()) {
           $resultRedirect = $this->resultRedirectFactory->create();
           $resultRedirect->setPath('customer/account/login');
           return $resultRedirect;
        }

        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect('*/*/');
            return;
        }
        
        try {
            $error = false;
            if (!\Zend_Validate::is(trim($post['commission_amount']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($post['user_id']), 'NotEmpty')) {
                $error = true;
            }

            if (!\Zend_Validate::is(trim($post['user_name']), 'NotEmpty')) {
                $error = true;
            }
            
            if ($error) {
                throw new \Exception();
            }

            $model = $this->redemptionFactory;
            $model->setData($post);
            $model->save();

            $notification['name'] = $post['user_name'];
            $notification['id'] = $model->getId();
            $_notification = $this->notificationFactory;
            $_notification->commissionRedemNotificationMessage($notification);

            $this->messageManager->addSuccess(
                __('Your Redemption request has been submitted.')
            );
            $this->_redirect('designer/commission/redemption');
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Your request has not been submitted.')
            );
            $this->_redirect('designer/commission/redemption');
            return;
        }
    }
}
