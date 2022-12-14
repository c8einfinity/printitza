<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Block\DesignIdea;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Customer Reviews list block
 *
 * @api
 * @since 100.0.2
 */
class DesignList extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Product reviews collection
     *
     * @var \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection
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
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $customerAccountManagement
     * @param \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $customerAccountManagement,
        \Designnbuy\DesignReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $customerSession,
            $subscriberFactory,
            $customerRepository,
            $customerAccountManagement,
            $data
        );
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Get html code for toolbar
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Initializes toolbar
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        if ($this->getReviews()) {
            $toolbar = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'customer_review_list.toolbar'
            )->setCollection(
                $this->getReviews()
            );

            $this->setChild('toolbar', $toolbar);
        }
        return parent::_prepareLayout();
    }

    /**
     * Get reviews
     *
     * @return bool|\Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection
     */
    public function getReviews()
    {
        $designId = $this->getRequest()->getParam('id', false);
        if (!$this->_collection) {
            $this->_collection = $this->_collectionFactory->create();
            $this->_collection
                ->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addFieldToFilter('entity_pk_value',$designId)
                //->addCustomerFilter($customerId)
                ->setDateOrder();
        }
        return $this->_collection;
    }

    /**
     * Get review link
     *
     * @return string
     * @deprecated 100.2.0
     */
    public function getReviewLink()
    {
        return $this->getUrl('designreview/customer/view/');
    }

    /**
     * Get review URL
     *
     * @param \Designnbuy\DesignReview\Model\Review $review
     * @return string
     * @since 100.2.0
     */
    public function getReviewUrl($review)
    {
        return $this->getUrl('designreview/customer/view', ['id' => $review->getReviewId()]);
    }

    /**
     * Get product link
     *
     * @return string
     * @deprecated 100.2.0
     */
    public function getProductLink()
    {
        return $this->getUrl('catalog/product/view/');
    }

    /**
     * Get product URL
     *
     * @param \Designnbuy\DesignReview\Model\Review $review
     * @return string
     * @since 100.2.0
     */
    public function getProductUrl($review)
    {
        return $this->getUrl('catalog/product/view', ['id' => $review->getEntityPkValue()]);
    }

    /**
     * Format date in short format
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::SHORT);
    }

    /**
     * Add review summary
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    protected function _beforeToHtml()
    {
        $reviews = $this->getReviews();
        if ($reviews) {
            $reviews->load()->addReviewSummary();
        }
        return parent::_beforeToHtml();
    }
}
