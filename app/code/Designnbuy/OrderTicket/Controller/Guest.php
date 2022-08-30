<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller;

use Designnbuy\OrderTicket\Model\OrderTicket;

abstract class Guest extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $orderticketHelper;

    /**
     * @var \Magento\Sales\Helper\Guest
     */
    protected $salesGuestHelper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketHelper
     * @param \Magento\Sales\Helper\Guest $salesGuestHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\OrderTicket\Helper\Data $orderticketHelper,
        \Magento\Sales\Helper\Guest $salesGuestHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
        $this->orderticketHelper = $orderticketHelper;
        $this->salesGuestHelper = $salesGuestHelper;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Check order view availability
     *
     * @param   \Designnbuy\OrderTicket\Model\OrderTicket $orderticket
     * @return  bool
     */
    protected function _canViewOrderTicket($orderticket)
    {
        $currentOrder = $this->_coreRegistry->registry('current_order');
        if ($orderticket->getOrderId() && $orderticket->getOrderId() === $currentOrder->getId()) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid orderticket by entity_id and register it
     *
     * @param int $entityId
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\Result\Forward|bool
     */
    protected function _loadValidOrderTicket($entityId = null)
    {
        if (!$this->orderticketHelper->isEnabled()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $result = $this->salesGuestHelper->loadValidOrder($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        if (null === $entityId) {
            $entityId = (int)$this->getRequest()->getParam('entity_id');
        }

        if (!$entityId) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }
        /** @var $orderticket \Designnbuy\OrderTicket\Model\OrderTicket */
        $orderticket = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket')->load($entityId);

        if ($this->_canViewOrderTicket($orderticket)) {
            $this->_coreRegistry->register('current_orderticket', $orderticket);
            return true;
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/returns');
    }
}
