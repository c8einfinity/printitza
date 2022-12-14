<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block;

/**
 * Review detailed view block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class View extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * View template name
     *
     * @var string
     */
    protected $_template = 'view.phtml';

    /**
     * Rating option model
     *
     * @var \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Rating model factory
     *
     * @var \Designnbuy\DesignReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * Review model
     *
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Designnbuy\DesignReview\Model\RatingFactory $ratingFactory
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory $voteFactory,
        \Designnbuy\DesignReview\Model\RatingFactory $ratingFactory,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory,
        array $data = []
    ) {
        $this->_voteFactory = $voteFactory;
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Retrieve current product model from registry
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProductData()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Retrieve current review model from registry
     *
     * @return \Designnbuy\DesignReview\Model\Review
     */
    public function getReviewData()
    {
        return $this->_coreRegistry->registry('current_review');
    }

    /**
     * Prepare link to review list for current product
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/list', ['id' => $this->getProductData()->getId()]);
    }

    /**
     * Retrieve collection of ratings
     *
     * @return \Designnbuy\DesignReview\Model\ResourceModel\Rating\Option\Vote\Collection
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = $this->_voteFactory->create()->getResourceCollection()->setReviewFilter(
                $this->getReviewId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addRatingInfo(
                $this->_storeManager->getStore()->getId()
            )->load();
            $this->setRatingCollection($ratingCollection->getSize() ? $ratingCollection : false);
        }
        return $this->getRatingCollection();
    }

    /**
     * Retrieve rating summary for current product
     *
     * @return string
     */
    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache(
                $this->_ratingFactory->create()->getEntitySummary($this->getProductData()->getId())
            );
        }
        return $this->getRatingSummaryCache();
    }

    /**
     * Retrieve total review count for current product
     *
     * @return string
     */
    public function getTotalReviews()
    {
        if (!$this->getTotalReviewsCache()) {
            $this->setTotalReviewsCache(
                $this->_reviewFactory->create()->getTotalReviews(
                    $this->getProductData()->getId(),
                    false,
                    $this->_storeManager->getStore()->getId()
                )
            );
        }
        return $this->getTotalReviewsCache();
    }

    /**
     * Format date in long format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get product reviews summary
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if (!$product->getRatingSummary()) {
            $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }
        return parent::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }
}
