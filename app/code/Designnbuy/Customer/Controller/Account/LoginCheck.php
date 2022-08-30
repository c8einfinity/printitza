<?php

namespace Designnbuy\Customer\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\Action\Action;

class LoginCheck extends Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var FormKey
     */
    protected $formKey;

    /**
     * Wishlist constructor.
     * @param Session $customerSession
     * @param JsonFactory $resultJsonFactory
     * @param Context $context
     */
    public function __construct(
        Session $customerSession,
        JsonFactory $resultJsonFactory,
        Context $context,
        \Magento\Framework\Data\Form\FormKey $formKey
    )
    {
        $this->customerSession = $customerSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->formKey = $formKey;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = [
            'error' => false,
            'status' => ''
        ];

        if($this->customerSession->isLoggedIn()) {
            $response['id'] = $this->customerSession->getCustomerId();
            $response['user_name'] = $this->customerSession->getCustomer()->getName();
            $response['status'] = 'true';
        } else {
            $response['status'] = 'false';
        }
        //$response['form_key'] = $this->getFormKey();
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($response);
    }

    /**
     * get form key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }
}