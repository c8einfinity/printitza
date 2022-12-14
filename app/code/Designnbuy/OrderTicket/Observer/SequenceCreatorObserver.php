<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\SalesSequence\Model\Builder;
use Magento\SalesSequence\Model\EntityPool;
use Magento\SalesSequence\Model\Config;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class CreateSequence
 */
class SequenceCreatorObserver implements ObserverInterface
{
    /**
     * @var Builder
     */
    private $sequenceBuilder;

    /**
     * @var EntityPool
     */
    private $entityPool;

    /**
     * @var Config
     */
    private $sequenceConfig;

    /**
     * Initialization
     *
     * @param Builder $sequenceBuilder
     * @param EntityPool $entityPool
     * @param Config $sequenceConfig
     */
    public function __construct(
        Builder $sequenceBuilder,
        EntityPool $entityPool,
        Config $sequenceConfig
    ) {
        $this->sequenceBuilder = $sequenceBuilder;
        $this->entityPool = $entityPool;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $storeId = $observer->getData('store')->getId();

        $entityType = 'orderticket';

        $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
            ->setSuffix($this->sequenceConfig->get('suffix'))
            ->setStartValue($this->sequenceConfig->get('startValue'))
            ->setStoreId($storeId)
            ->setStep($this->sequenceConfig->get('step'))
            ->setWarningValue($this->sequenceConfig->get('warningValue'))
            ->setMaxValue($this->sequenceConfig->get('maxValue'))
            ->setEntityType($entityType)->create();
        return $this;
    }
}
