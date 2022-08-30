<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel;

/**
 * Customer design resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Design extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * DB connection
     *
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * Name of design link DB table
     *
     * @var string
     */
    protected $_designLinkTable;

    /**
     * Name of scope for error messages
     *
     * @var string
     */
    protected $_messagesScope = 'customer/session';

    /**
     * Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $mathRandom;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Math\Random $mathRandom,
        $connectionName = null
    ) {
        $this->_date = $date;
        $this->mathRandom = $mathRandom;
        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_customer_design', 'design_id');
        $this->_designLinkTable = $this->getTable('designnbuy_customer_queue_link');
        $this->connection = $this->getConnection();
    }

    /**
     * Set error messages scope
     *
     * @param string $scope
     * @return void
     */
    public function setMessagesScope($scope)
    {
        $this->_messagesScope = $scope;
    }

    /**
     * Load design from DB by email
     *
     * @param string $designEmail
     * @return array
     */
    public function loadByEmail($designEmail)
    {
        $select = $this->connection->select()->from($this->getMainTable())->where('design_email=:design_email');

        $result = $this->connection->fetchRow($select, ['design_email' => $designEmail]);

        if (!$result) {
            return [];
        }

        return $result;
    }

    /**
     * Load design by customer
     *
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return array
     */
    public function loadByCustomerData(\Magento\Customer\Api\Data\CustomerInterface $customer)
    {
        $select = $this->connection->select()->from($this->getMainTable())->where('customer_id=:customer_id');

        $result = $this->connection->fetchRow($select, ['customer_id' => $customer->getId()]);

        if ($result) {
            return $result;
        }

        $select = $this->connection->select()->from($this->getMainTable())->where('design_email=:design_email');

        $result = $this->connection->fetchRow($select, ['design_email' => $customer->getEmail()]);

        if ($result) {
            return $result;
        }

        return [];
    }

    /**
     * Generates random code for subscription confirmation
     *
     * @return string
     */
    protected function _generateRandomCode()
    {
        return $this->mathRandom->getUniqueHash();
    }

    /**
     * Updates data when design received
     *
     * @param \Designnbuy\Customer\Model\Design $design
     * @param \Designnbuy\Customer\Model\Queue $queue
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function received(\Designnbuy\Customer\Model\Design $design, \Designnbuy\Customer\Model\Queue $queue)
    {
        $this->connection->beginTransaction();
        try {
            $data['letter_sent_at'] = $this->_date->gmtDate();
            $this->connection->update(
                $this->_designLinkTable,
                $data,
                ['design_id = ?' => $design->getId(), 'queue_id = ?' => $queue->getId()]
            );
            $this->connection->commit();
        } catch (\Exception $e) {
            $this->connection->rollBack();
            throw new \Magento\Framework\Exception\LocalizedException(__('We cannot mark as received design.'));
        }
        return $this;
    }
}
