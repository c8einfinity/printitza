<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Request Details Block at ORDERTICKET page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General;

class Details extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General\AbstractGeneral
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    protected $_gridTicketFactory;

    protected $_jobmanagementFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\OrderTicket\Model\GridFactory $gridTicketFactory,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobmanagementFactory,
        array $data = []
    ) {
        $this->_gridTicketFactory = $gridTicketFactory;
        $this->_jobmanagementFactory = $jobmanagementFactory;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Get order link (href address)
     *
     * @return string
     */
    public function getOrderLink()
    {
        return $this->getUrl('sales/order/view', ['order_id' => $this->getOrder()->getId()]);
    }

    /**
     * Get order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    /**
     * Get Link to Customer's Page
     *
     * Gets address for link to customer's page.
     * Returns null for guest-checkout orders
     *
     * @return string|null
     */
    public function getCustomerLink()
    {
        if ($this->getOrder()->getCustomerIsGuest()) {
            return false;
        }
        return $this->getUrl('customer/index/edit', ['id' => $this->getOrder()->getCustomerId()]);
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerEmail()
    {
        return $this->escapeHtml($this->getOrder()->getCustomerEmail());
    }

    /**
     * Get Customer Email
     *
     * @return string
     */
    public function getCustomerContactEmail()
    {
        return $this->escapeHtml($this->getOrderTicketData('customer_custom_email'));
    }

    public function getJobLink(){
        $orderTicket = $this->getTicketData();
        $jobId = $orderTicket->getJobId();
        return $this->getUrl('jobmanagement/jobmanagement/edit', ['id' => $jobId]);
    }

    public function getJobId() {
        $orderTicket = $this->getTicketData();
        $jobId = $orderTicket->getJobId();
        return $jobId;
    }

    public function getJobTitle() {
        $orderTicket = $this->getTicketData();
        $jobId = $orderTicket->getJobId();
        $jobs = $this->_jobmanagementFactory->create()->load($jobId);
        $jobTitle = $jobs->getTitle();
        return $jobTitle;
    }

    public function getTicketData() {
        $id = $this->getRequest()->getParam('id');
        $orderTicket = $this->_gridTicketFactory->create()->load($id);
        return $orderTicket;
    }
    
}
