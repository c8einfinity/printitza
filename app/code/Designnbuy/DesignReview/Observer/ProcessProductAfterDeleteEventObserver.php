<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessProductAfterDeleteEventObserver implements ObserverInterface
{
    /**
     * Review resource model
     *
     * @var \Designnbuy\DesignReview\Model\ResourceModel\Review
     */
    protected $_resourceReview;

    /**
     * @var \Designnbuy\DesignReview\Model\ResourceModel\Rating
     */
    protected $_resourceRating;

    /**
     * @param \Designnbuy\DesignReview\Model\ResourceModel\Review $resourceReview
     * @param \Designnbuy\DesignReview\Model\ResourceModel\Rating $resourceRating
     */
    public function __construct(
        \Designnbuy\DesignReview\Model\ResourceModel\Review $resourceReview,
        \Designnbuy\DesignReview\Model\ResourceModel\Rating $resourceRating
    ) {
        $this->_resourceReview = $resourceReview;
        $this->_resourceRating = $resourceRating;
    }

    /**
     * Cleanup product reviews after product delete
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventProduct = $observer->getEvent()->getProduct();
        if ($eventProduct && $eventProduct->getId()) {
            $this->_resourceReview->deleteReviewsByProductId($eventProduct->getId());
            $this->_resourceRating->deleteAggregatedRatingsByProductId($eventProduct->getId());
        }

        return $this;
    }
}
