<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Adminhtml\Product;

use Designnbuy\DesignReview\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

class NewAction extends ProductController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Designnbuy_DesignReview::catalog_reviews_ratings_reviews_all');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
        $resultPage->getConfig()->getTitle()->prepend(__('New Review'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(\Designnbuy\DesignReview\Block\Adminhtml\Add::class));
        $resultPage->addContent($resultPage->getLayout()->createBlock(
            \Designnbuy\DesignReview\Block\Adminhtml\Product\Grid::class
        ));
        return $resultPage;
    }
}
