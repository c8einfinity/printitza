<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

/**
 * Adminhtml reviews grid
 *
 * @method int getProductId() getProductId()
 * @method \Designnbuy\DesignReview\Block\Adminhtml\Grid setProductId() setProductId(int $productId)
 * @method int getCustomerId() getCustomerId()
 * @method \Designnbuy\DesignReview\Block\Adminhtml\Grid setCustomerId() setCustomerId(int $customerId)
 * @method \Designnbuy\DesignReview\Block\Adminhtml\Grid setMassactionIdFieldOnlyIndexValue() setMassactionIdFieldOnlyIndexValue(bool $onlyIndex)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\DesignReview\Block\Adminhtml;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Review action pager
     *
     * @var \Designnbuy\DesignReview\Helper\Action\Pager
     */
    protected $_reviewActionPager = null;

    /**
     * Review data
     *
     * @var \Designnbuy\DesignReview\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Review collection model factory
     *
     * @var \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory
     */
    protected $_productsFactory;

    /**
     * Review model factory
     *
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
     * @param \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory
     * @param \Designnbuy\DesignReview\Helper\Data $reviewData
     * @param \Designnbuy\DesignReview\Helper\Action\Pager $reviewActionPager
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory,
        \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\CollectionFactory $productsFactory,
        \Designnbuy\DesignReview\Helper\Data $reviewData,
        \Designnbuy\DesignReview\Helper\Action\Pager $reviewActionPager,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_productsFactory = $productsFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_reviewData = $reviewData;
        $this->_reviewActionPager = $reviewActionPager;
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * Initialize grid
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('reviwGrid');
        $this->setDefaultSort('created_at');
    }

    /**
     * Save search results
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _afterLoadCollection()
    {
        /** @var $actionPager \Designnbuy\DesignReview\Helper\Action\Pager */
        $actionPager = $this->_reviewActionPager;
        $actionPager->setStorageId('reviews');
        $actionPager->setItems($this->getCollection()->getResultingIds());

        return parent::_afterLoadCollection();
    }

    /**
     * Prepare collection
     *
     * @return \Designnbuy\DesignReview\Block\Adminhtml\Grid
     */
    protected function _prepareCollection()
    {
        /** @var $model \Designnbuy\DesignReview\Model\Review */
        $model = $this->_reviewFactory->create();
        /** @var $collection \Designnbuy\DesignReview\Model\ResourceModel\Review\Product\Collection */
        $collection = $this->_productsFactory->create();

        if ($this->getProductId() || $this->getRequest()->getParam('productId', false)) {
            $productId = $this->getProductId();
            if (!$productId) {
                $productId = $this->getRequest()->getParam('productId');
            }
            $this->setProductId($productId);
            $collection->addEntityFilter($this->getProductId());
        }

        if ($this->getCustomerId() || $this->getRequest()->getParam('customerId', false)) {
            $customerId = $this->getCustomerId();
            if (!$customerId) {
                $customerId = $this->getRequest()->getParam('customerId');
            }
            $this->setCustomerId($customerId);
            $collection->addCustomerFilter($this->getCustomerId());
        }

        if ($this->_coreRegistry->registry('usePendingFilter') === true) {
            $collection->addStatusFilter($model->getPendingStatus());
        }

        $collection->addStoreData();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return \Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'review_id',
            [
                'header' => __('ID'),
                'filter_index' => 'rt.review_id',
                'index' => 'review_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created'),
                'type' => 'datetime',
                'filter_index' => 'rt.created_at',
                'index' => 'review_created_at',
                'header_css_class' => 'col-date',
                'column_css_class' => 'col-date'
            ]
        );

        if (!$this->_coreRegistry->registry('usePendingFilter')) {
            $this->addColumn(
                'status',
                [
                    'header' => __('Status'),
                    'type' => 'options',
                    'options' => $this->_reviewData->getReviewStatuses(),
                    'filter_index' => 'rt.status_id',
                    'index' => 'status_id'
                ]
            );
        }

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'filter_index' => 'rdt.title',
                'index' => 'title',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true
            ]
        );

        $this->addColumn(
            'nickname',
            [
                'header' => __('Nickname'),
                'filter_index' => 'rdt.nickname',
                'index' => 'nickname',
                'type' => 'text',
                'truncate' => 50,
                'escape' => true,
                'header_css_class' => 'col-name',
                'column_css_class' => 'col-name'
            ]
        );

        $this->addColumn(
            'detail',
            [
                'header' => __('Review'),
                'index' => 'detail',
                'filter_index' => 'rdt.detail',
                'type' => 'text',
                'truncate' => 50,
                'nl2br' => true,
                'escape' => true
            ]
        );

        /**
         * Check is single store mode
         */
        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'visible_in',
                ['header' => __('Visibility'), 'index' => 'stores', 'type' => 'store', 'store_view' => true]
            );
        }

        $this->addColumn(
            'type',
            [
                'header' => __('Type'),
                'type' => 'select',
                'index' => 'type',
                'filter' => \Designnbuy\DesignReview\Block\Adminhtml\Grid\Filter\Type::class,
                'renderer' => \Designnbuy\DesignReview\Block\Adminhtml\Grid\Renderer\Type::class
            ]
        );

        /*$this->addColumn(
            'name',
            ['header' => __('Product'), 'type' => 'text', 'index' => 'name', 'escape' => true]
        );

        $this->addColumn(
            'sku',
            [
                'header' => __('SKU'),
                'type' => 'text',
                'index' => 'sku',
                'escape' => true
            ]
        );*/

        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'type' => 'action',
                'getter' => 'getReviewId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => 'designreview/product/edit',
                            'params' => [
                                'productId' => $this->getProductId(),
                                'customerId' => $this->getCustomerId(),
                                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null,
                            ],
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter' => false,
                'sortable' => false
            ]
        );

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

    /**
     * Prepare grid mass actions
     *
     * @return void
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('review_id');
        $this->setMassactionIdFilter('rt.review_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->getMassactionBlock()->setFormFieldName('reviews');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/*/massDelete',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_reviewData->getReviewStatusesOptionArray();
        array_unshift($statuses, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'update_status',
            [
                'label' => __('Update Status'),
                'url' => $this->getUrl(
                    '*/*/massUpdateStatus',
                    ['ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : 'index']
                ),
                'additional' => [
                    'status' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses,
                    ],
                ]
            ]
        );
    }

    /**
     * Get row url
     *
     * @param \Designnbuy\DesignReview\Model\Review|\Magento\Framework\DataObject $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            'designreview/product/edit',
            [
                'id' => $row->getReviewId(),
                'productId' => $this->getProductId(),
                'customerId' => $this->getCustomerId(),
                'ret' => $this->_coreRegistry->registry('usePendingFilter') ? 'pending' : null
            ]
        );
    }

    /**
     * Get grid url
     *
     * @return string
     */
    public function getGridUrl()
    {
        if ($this->getProductId() || $this->getCustomerId()) {
            return $this->getUrl(
                'designreview/product' . ($this->_coreRegistry->registry('usePendingFilter') ? 'pending' : ''),
                ['productId' => $this->getProductId(), 'customerId' => $this->getCustomerId()]
            );
        } else {
            return $this->getCurrentUrl();
        }
    }
}
