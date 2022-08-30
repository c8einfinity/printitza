<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\Order\View\Tab;

/**
 * Order ORDERTICKET Grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class OrderTicket extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\OrderTicket\Helper\Data $orderticketHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->orderticketHelper = $orderticketHelper;
        parent::__construct($context, $backendHelper, $collectionFactory, $orderticketFactory, $moduleManager, $data);
    }

    /**
     * Initialize order orderticket
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('order_orderticket');
        $this->setUseAjax(true);
    }

    /**
     * Configuring and setting collection
     *
     * @return $this
     */
    protected function _beforePrepareCollection()
    {
        $orderId = null;

        if ($this->getOrder() && $this->getOrder()->getId()) {
            $orderId = $this->getOrder()->getId();
        } elseif ($this->getOrderId()) {
            $orderId = $this->getOrderId();
        }
        if ($orderId) {
            /** @var $collection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
            $collection = $this->_collectionFactory->create()->addFieldToFilter('order_id', $orderId);
            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid|void
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        unset($this->_columns['order_increment_id']);
        unset($this->_columns['order_date']);
    }

    /**
     * Get Url to action
     *
     * @param string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return 'adminhtml/orderticket/' . $action;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/orderticket/orderticketOrder', ['_current' => true]);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * ######################## TAB settings #################################
     */
    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Order Tickets');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Order Tickets');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->orderticketHelper->canCreateOrderTicket($this->getOrder());
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
