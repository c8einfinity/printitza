<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Adminhtml\Product;

use Designnbuy\DesignReview\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

class Pending extends ProductController
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            $this->coreRegistry->register('usePendingFilter', true);
            /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('reviewGrid');
            return $resultForward;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
        $resultPage->getConfig()->getTitle()->prepend(__('Pending Reviews'));
        $this->coreRegistry->register('usePendingFilter', true);
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Designnbuy\DesignReview\Block\Adminhtml\Main::class));
        return $resultPage;
    }
}
