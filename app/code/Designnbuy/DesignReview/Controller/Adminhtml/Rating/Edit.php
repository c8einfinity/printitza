<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Adminhtml\Rating;

use Designnbuy\DesignReview\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends RatingController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->initEnityId();
        /** @var \Designnbuy\DesignReview\Model\Rating $ratingModel */
        $ratingModel = $this->_objectManager->create(\Designnbuy\DesignReview\Model\Rating::class);
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Designnbuy_DesignReview::catalog_reviews_ratings_ratings');
        $resultPage->getConfig()->getTitle()->prepend(__('Ratings'));
        $resultPage->getConfig()->getTitle()->prepend(
            $ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating')
        );
        $resultPage->addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));
        $resultPage->addContent($resultPage->getLayout()->createBlock(
            \Designnbuy\DesignReview\Block\Adminhtml\Rating\Edit::class
        ))->addLeft($resultPage->getLayout()->createBlock(\Designnbuy\DesignReview\Block\Adminhtml\Rating\Edit\Tabs::class));
        return $resultPage;
    }
}
