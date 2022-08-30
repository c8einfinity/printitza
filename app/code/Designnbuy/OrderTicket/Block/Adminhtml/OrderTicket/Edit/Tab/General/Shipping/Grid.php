<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General\Shipping;

/**
 * Grid of packaging shipment
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Grid extends \Magento\Backend\Block\Template
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
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_orderticketData = $orderticketData;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Return collection of shipment items
     *
     * @return array|bool
     */
    public function getCollection()
    {
        return $this->_coreRegistry->registry('current_orderticket')->getShippingMethods(true);
    }

    /**
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->_coreRegistry->registry('current_orderticket')->getStoreId();
        $order = $this->_coreRegistry->registry('current_orderticket')->getOrder();
        $address = $order->getShippingAddress();
        $shippingSourceCountryCode = $address->getCountryId();

        $shippingDestinationInfo = $this->_orderticketData->getReturnAddressModel($storeId);
        $shippingDestinationCountryCode = $shippingDestinationInfo->getCountryId();

        if ($shippingSourceCountryCode != $shippingDestinationCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Foordertickett price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
