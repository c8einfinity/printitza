<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model;

/**
 * ORDERTICKET model
 */
class Grid extends \Magento\Framework\Model\AbstractModel
{
    /**
     * OrderTicket source status factory
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory
     */
    protected $_statusFactory;

    /**
     * @var string
     */
    protected $statusLabel;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory $statusFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\OrderTicket\Model\OrderTicket\Source\StatusFactory $statusFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_statusFactory = $statusFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\OrderTicket\Model\ResourceModel\Grid');
        parent::_construct();
    }

    /**
     * Get ORDERTICKET's status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if (!$this->statusLabel) {
            /** @var $sourceStatus \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status */
            $sourceStatus = $this->_statusFactory->create();
            $this->statusLabel = $sourceStatus->getItemLabel($this->getStatus());
        }
        return $this->statusLabel;
    }
}
