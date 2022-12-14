<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Customer;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Designnbuy\DesignReview\Controller\Customer as CustomerController;
use Designnbuy\DesignReview\Model\ReviewFactory;

class View extends CustomerController
{
    /**
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        ReviewFactory $reviewFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        parent::__construct($context, $customerSession);
    }

    /**
     * Render review details
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $review = $this->reviewFactory->create()->load($this->getRequest()->getParam('id'));
        if ($review->getCustomerId() != $this->customerSession->getCustomerId()) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            /*$resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('noroute');
            return $resultForward;*/
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        if ($navigationBlock = $resultPage->getLayout()->getBlock('customer_account_navigation')) {
            $navigationBlock->setActive('designreview/customer');
        }
        $resultPage->getConfig()->getTitle()->set(__('Review Details'));
        return $resultPage;
    }
}
