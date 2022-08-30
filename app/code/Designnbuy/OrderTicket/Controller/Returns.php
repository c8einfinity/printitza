<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Designnbuy\OrderTicket\Controller;

use Magento\Framework\App\RequestInterface;
use Designnbuy\OrderTicket\Model\OrderTicket;

abstract class Returns extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Check customer authentication for some actions
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl();

        if (!$this->_objectManager->get('Magento\Customer\Model\Session')->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Check order view availability
     *
     * @param \Designnbuy\OrderTicket\Model\OrderTicket|\Magento\Sales\Model\Order $item
     * @return bool
     */
    protected function _canViewOrder($item)
    {
        $customerId = $this->_objectManager->get('Magento\Customer\Model\Session')->getCustomerId();
        if ($item->getId() && $customerId && $item->getCustomerId() == $customerId) {
            return true;
        }
        return false;
    }

    /**
     * Try to load valid orderticket by entity_id and register it
     *
     * @param int $entityId
     * @return bool
     */
    protected function _loadValidOrderTicket($entityId = null)
    {
        $entityId = $entityId ?: (int) $this->getRequest()->getParam('entity_id');
        if (!$entityId || !$this->_isEnabledOnFront()) {
            $this->_forward('noroute');
            return false;
        }

        /** @var $orderticket \Designnbuy\OrderTicket\Model\OrderTicket */
        $orderticket = $this->_objectManager->create('Designnbuy\OrderTicket\Model\OrderTicket')->load($entityId);
        if ($this->_canViewOrder($orderticket)) {
            $this->_coreRegistry->register('current_orderticket', $orderticket);
            return true;
        } else {
            $this->_redirect('*/*/history');
        }
        return false;
    }

    /**
     * Checks whether ORDERTICKET module is enabled in system config
     *
     * @return boolean
     */
    protected function _isEnabledOnFront()
    {
        /** @var $orderticketHelper \Designnbuy\OrderTicket\Helper\Data */
        $orderticketHelper = $this->_objectManager->get('Designnbuy\OrderTicket\Helper\Data');
        return $orderticketHelper->isEnabled();
    }
}
