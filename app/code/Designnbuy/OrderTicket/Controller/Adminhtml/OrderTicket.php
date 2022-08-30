<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Designnbuy\OrderTicket\Model\OrderTicket as OrderTicketModel;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class OrderTicket extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_OrderTicket::designnbuy_orderticket';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Application filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * Read directory
     *
     * @var \Magento\Framework\Filesystem\Directory\Read
     */
    protected $readDirectory;

    /**
     * Http response file factory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Shipping carrier helper
     *
     * @var \Magento\Shipping\Helper\Carrier
     */
    protected $carrierHelper;

    /**
     * @var \Designnbuy\OrderTicket\Model\OrderTicket\OrderTicketDataMapper
     */
    protected $orderticketDataMapper;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param OrderTicketModel\OrderTicketDataMapper $orderticketDataMapper
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Shipping\Helper\Carrier $carrierHelper,
        \Designnbuy\OrderTicket\Model\OrderTicket\OrderTicketDataMapper $orderticketDataMapper
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->readDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->_fileFactory = $fileFactory;

        $this->orderticketDataMapper = $orderticketDataMapper;
        parent::__construct($context);
    }

    /**
     * Init active menu and set breadcrumb
     *
     * @return \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Designnbuy_OrderTicket::sales_designnbuy_orderticket_orderticket');

        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Order Tickets'));
        return $this;
    }

    /**
     * Initialize model
     *
     * @param string $requestParam
     * @return \Designnbuy\OrderTicket\Model\OrderTicket
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _initModel($requestParam = 'id')
    {
        /** @var $model \Designnbuy\OrderTicket\Model\OrderTicket */
        $model = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket');
        $model->setStoreId($this->getRequest()->getParam('store', 0));

        $orderticketId = $this->getRequest()->getParam($requestParam);
        if ($orderticketId) {
            $model->load($orderticketId);
            if (!$model->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The wrong Order Ticket was requested.'));
            }
            $this->_coreRegistry->register('current_orderticket', $model);
            $orderId = $model->getOrderId();
        } else {
            $orderId = $this->getRequest()->getParam('order_id');
        }

        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            if (!$order->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('This is the wrong Order Ticket ID.'));
            }
            $this->_coreRegistry->register('current_order', $order);
        }

        return $model;
    }

    /**
     * Initialize model
     *
     * @return \Designnbuy\OrderTicket\Model\OrderTicket\Create
     */
    protected function _initCreateModel()
    {
        /** @var $model \Designnbuy\OrderTicket\Model\OrderTicket\Create */
        $model = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket\Create');
        $orderId = $this->getRequest()->getParam('order_id');
        $model->setOrderId($orderId);
        if ($orderId) {
            /** @var $order \Magento\Sales\Model\Order */
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
            $model->setCustomerId($order->getCustomerId());
            $model->setStoreId($order->getStoreId());
        }
        $this->_coreRegistry->register('orderticket_create_model', $model);
        return $model;
    }
}
