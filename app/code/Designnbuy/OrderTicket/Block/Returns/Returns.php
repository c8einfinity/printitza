<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Returns;

use Magento\Customer\Model\Context;

class Returns extends \Magento\Framework\View\Element\Template
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * OrderTicket grid collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->_orderticketData = $orderticketData;
        $this->_coreRegistry = $registry;
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        $this->httpContext = $httpContext;
        $this->_isScopePrivate = true;
        parent::__construct($context, $data);
    }

    /**
     * Initialize returns content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        if ($this->_orderticketData->isEnabled()) {
            $this->setTemplate('return/returns.phtml');
            /** @var $returns \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
            $returns = $this->_collectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'order_id',
                $this->_coreRegistry->registry('current_order')->getId()
            )->setOrder(
                'date_requested',
                'desc'
            );

            if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
                $returns->addFieldToFilter('customer_id', $this->_customerSession->getCustomer()->getId());
            }
            $this->setReturns($returns);
        }
    }

    /**
     * Prepare orderticket returns layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'sales.order.history.pager'
        )->setCollection(
            $this->getReturns()
        );
        $this->setChild('pager', $pager);
        $this->getReturns()->load();
        return $this;
    }

    /**
     * Get pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get orderticket returns view url
     *
     * @param \Magento\Framework\DataObject $return
     * @return string
     */
    public function getViewUrl($return)
    {
        return $this->getUrl('*/*/view', ['entity_id' => $return->getId()]);
    }

    /**
     * Get sales order history url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('sales/order/history');
    }

    /**
     * Get sales order reorder url
     *
     * @param \Magento\Framework\DataObject $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('sales/order/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * Get sales guest print url
     *
     * @param \Magento\Framework\DataObject $order
     * @return string
     */
    public function getPrintUrl($order)
    {
        return $this->getUrl('sales/guest/print', ['order_id' => $order->getId()]);
    }
}
