<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create;

/**
 * Admin ORDERTICKET create form header
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class AbstractCreate extends \Magento\Backend\Block\Widget
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve create order model object
     *
     * @return \Designnbuy\OrderTicket\Model\OrderTicket\Create
     */
    public function getCreateOrderTicketModel()
    {
        return $this->_coreRegistry->registry('orderticket_create_model');
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getCustomerId()
    {
        return (int)$this->getCreateOrderTicketModel()->getCustomerId();
    }

    /**
     * Retrieve customer identifier
     *
     * @return int
     */
    public function getStoreId()
    {
        return (int)$this->getCreateOrderTicketModel()->getStoreId();
    }

    /**
     * Retrieve customer object
     *
     * @return int
     */
    public function getCustomer()
    {
        return $this->getCreateOrderTicketModel()->getCustomer();
    }

    /**
     * Retrieve customer name
     *
     * @return int
     */
    public function getCustomerName()
    {
        return $this->escapeHtml($this->getCustomer()->getName());
    }

    /**
     * Retrieve order identifier
     *
     * @return int
     */
    public function getOrderId()
    {
        return (int)$this->getCreateOrderTicketModel()->getOrderId();
    }

    /**
     * Set Customer Id
     *
     * @param int $id
     * @return void
     */
    public function setCustomerId($id)
    {
        $this->getCreateOrderTicketModel()->setCustomerId($id);
    }

    /**
     * Set Order Id
     *
     * @param int $id
     * @return mixed
     */
    public function setOrderId($id)
    {
        return $this->getCreateOrderTicketModel()->setOrderId($id);
    }
}
