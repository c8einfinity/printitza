<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\Customer;

use Magento\Catalog\Model\Product;
use Designnbuy\DesignReview\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;
use Designnbuy\DesignReview\Model\Review;

/**
 * Customer Review detailed view block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'customer/view.phtml';

    /**
     * Catalog product model
     *
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Review model
     *
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * Rating option model
     *
     * @var \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Rating model
     *
     * @var \Designnbuy\DesignReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Designnbuy\DesignReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory,
        \Designnbuy\DesignReview\Model\Rating\Option\VoteFactory $voteFactory,
        \Designnbuy\DesignReview\Model\RatingFactory $ratingFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->_reviewFactory = $reviewFactory;
        $this->_voteFactory = $voteFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->currentCustomer = $currentCustomer;
        $this->_designideaFactory = $designideaFactory;
        $this->_templateFactory = $templateFactory;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize review id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setReviewId($this->getRequest()->getParam('id', false));
    }

    /**
     * Get product data
     *
     * @return Product
     */
    public function getDesignData()
    {
        if ($this->getReviewId() && !$this->getDesignCacheData()) {
            if($this->getReviewData()->getEntityId() == 1){
                $design = $this->_designideaFactory->create()->load($this->getReviewData()->getEntityPkValue());
            } elseif ($this->getReviewData()->getEntityId() == 2){
                $design = $this->_templateFactory->create()->load($this->getReviewData()->getEntityPkValue());
            }
            //$product = $this->productRepository->getById($this->getReviewData()->getEntityPkValue());
            $this->setDesignCacheData($design);
        }
        return $this->getDesignCacheData();
    }

    /**
     * Get review data
     *
     * @return Review
     */
    public function getReviewData()
    {
        if ($this->getReviewId() && !$this->getReviewCachedData()) {
            $this->setReviewCachedData($this->_reviewFactory->create()->load($this->getReviewId()));
        }
        return $this->getReviewCachedData();
    }

    /**
     * Return review customer url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('designreview/customer');
    }

    /**
     * Get review rating collection
     *
     * @return VoteCollection
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = $this->_voteFactory->create()->getResourceCollection()->setReviewFilter(
                $this->getReviewId()
            )->addRatingInfo(
                $this->_storeManager->getStore()->getId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->load();

            $this->setRatingCollection($ratingCollection->getSize() ? $ratingCollection : false);
        }

        return $this->getRatingCollection();
    }

    /**
     * Get rating summary
     *
     * @return array
     */
    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache(
                $this->_ratingFactory->create()->getEntitySummary($this->getDesignData()->getId())
            );
        }
        return $this->getRatingSummaryCache();
    }

    /**
     * Get total reviews
     *
     * @return int
     */
    public function getTotalReviews()
    {
        if (!$this->getTotalReviewsCache()) {
            $this->setTotalReviewsCache(
                $this->_reviewFactory->create()->getTotalReviews($this->getDesignData()->getId()),
                false,
                $this->_storeManager->getStore()->getId()
            );
        }
        return $this->getTotalReviewsCache();
    }

    /**
     * Get formatted date
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
        $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if (!$product->getRatingSummary()) {
            $this->_reviewFactory->create()->getEntitySummary($product, $this->_storeManager->getStore()->getId());
        }
        return parent::getReviewsSummaryHtml($product, $templateType, $displayIfNoReviews);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }
}
