<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General;

/**
 * Request Details Block at ORDERTICKET page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Returnaddress extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General\AbstractGeneral
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        array $data = []
    ) {
        $this->_orderticketData = $orderticketData;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Constructor
     *
     * @return void
     */
    public function _construct()
    {
        $order = $this->_coreRegistry->registry('current_order');
        $orderticket = $this->_coreRegistry->registry('current_orderticket');
        if ($order && $order->getId()) {
            $this->setStoreId($order->getStoreId());
        } elseif ($orderticket && $orderticket->getId()) {
            $this->setStoreId($orderticket->getStoreId());
        }
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getReturnAddress()
    {
        return $this->_orderticketData->getReturnAddress('html', [], $this->getStoreId());
    }
}
