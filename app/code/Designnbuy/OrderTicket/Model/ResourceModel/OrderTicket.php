<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\ResourceModel;

use Designnbuy\OrderTicket\Model\Spi\OrderTicketResourceInterface;

/**
 * ORDERTICKET entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class OrderTicket extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb implements OrderTicketResourceInterface
{
    /**
     * OrderTicket grid factory
     *
     * @var \Designnbuy\OrderTicket\Model\GridFactory
     */
    protected $orderticketGridFactory;

    /**
     * Eav configuration model
     *
     * @var \Magento\SalesSequence\Model\Manager
     */
    protected $sequenceManager;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Designnbuy\OrderTicket\Model\GridFactory $orderticketGridFactory
     * @param \Magento\SalesSequence\Model\Manager $sequenceManager
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Designnbuy\OrderTicket\Model\GridFactory $orderticketGridFactory,
        \Magento\SalesSequence\Model\Manager $sequenceManager,
        $connectionName = null
    ) {
        $this->orderticketGridFactory = $orderticketGridFactory;
        $this->sequenceManager = $sequenceManager;
        parent::__construct($context, $connectionName);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_orderticket', 'entity_id');
    }

    /**
     * Perform actions after object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _afterSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_afterSave($object);
        /** @var \Designnbuy\OrderTicket\Model\OrderTicket $object */
        /** @var $gridModel \Designnbuy\OrderTicket\Model\Grid */
        $gridModel = $this->orderticketGridFactory->create();
        $gridModel->addData($object->getData());
        $gridModel->save();
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Framework\DataObject $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        parent::_beforeSave($object);
        /** @var \Designnbuy\OrderTicket\Model\OrderTicket $object */
        if (!$object->getIncrementId()) {
            $incrementId = $this->sequenceManager->getSequence('orderticket', $object->getStoreId())->getNextValue();
            $object->setIncrementId($incrementId);
        }
        /*if (!$object->getIsUpdate()) {
            $object->setData(
                'protect_code',
                substr(
                    md5(uniqid(\Magento\Framework\Math\Random::getRandomNumber(), true) . ':' . microtime(true)),
                    5,
                    6
                )
            );
        }*/

        return $this;
    }
}
