<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\Customer;

use Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection;

/**
 * Recent Customer Reviews Block
 *
 * @api
 * @since 100.0.2
 */
class Recent extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer list template name
     *
     * @var string
     */
    protected $_template = 'customer/list.phtml';

    /**
     * Product reviews collection
     *
     * @var Collection
     */
    protected $_collection;

    /**
     * Review resource model
     *
     * @var \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context, $data);
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Truncate string
     *
     * @param string $value
     * @param int $length
     * @param string $etc
     * @param string &$remainder
     * @param bool $breakWords
     * @return string
     */
    public function truncateString($value, $length = 80, $etc = '...', &$remainder = '', $breakWords = true)
    {
        return $this->filterManager->truncate(
            $value,
            ['length' => $length, 'etc' => $etc, 'remainder' => $remainder, 'breakWords' => $breakWords]
        );
    }

    /**
     * Return collection of reviews
     *
     * @return array|\Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection
     */
    public function getReviews()
    {
        if (!($customerId = $this->currentCustomer->getCustomerId())) {
            return [];
        }
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addCustomerFilter($customerId)
                ->setDateOrder()
                ->setPageSize(5)
                ->load()
                ->addReviewSummary();
        }
        return $this->_collection;
    }

    /**
     * Return review customer view url
     *
     * @return string
     */
    public function getReviewLink()
    {
        return $this->getUrl('designreview/customer/view/');
    }

    /**
     * Return catalog product view url
     *
     * @return string
     */
    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    /**
     * Format review date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Return review customer url
     *
     * @return string
     */
    public function getAllReviewsUrl()
    {
        return $this->getUrl('designreview/customer');
    }

    /**
     * Return review customer view url for a specific customer/review
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('designreview/customer/view', ['id' => $id]);
    }
}
