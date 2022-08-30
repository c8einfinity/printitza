<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\ProductTypes\ConfigInterface;
use Magento\SalesSequence\Model\Builder;
use Magento\SalesSequence\Model\Config as SequenceConfig;

/**
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallData implements InstallDataInterface
{
    /**
     * ORDERTICKET setup factory
     *
     * @var OrderTicketSetupFactory
     */
    protected $orderticketSetupFactory;

    /**
     * OrderTicket refundable list
     *
     * @var ConfigInterface
     */
    protected $productTypeConfig;

    /**
     * @var Builder
     */
    protected $sequenceBuilder;

    /**
     * @var SequenceConfig
     */
    private $sequenceConfig;

    /**
     * @param OrderTicketSetupFactory $setupFactory
     * @param ConfigInterface $productTypeConfig
     * @param Builder $sequenceBuilder
     * @param SequenceConfig $sequenceConfig
     */
    public function __construct(
        OrderTicketSetupFactory $setupFactory,
        ConfigInterface $productTypeConfig,
        Builder $sequenceBuilder,
        SequenceConfig $sequenceConfig
    ) {
        $this->orderticketSetupFactory = $setupFactory;
        $this->productTypeConfig = $productTypeConfig;
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {

        /** @var RmaSetup $installer */
        $orderticketSetup = $this->orderticketSetupFactory->create(['setup' => $setup]);

        /**
         * Prepare database before module installation
         */
        $orderticketSetup->installEntities();

        $entities = $orderticketSetup->getDefaultEntities();
        foreach ($entities as $entityName => $entity) {
            $orderticketSetup->addEntityType($entityName, $entity);
        }

        $defaultStoreIds = [0, 1];
        $entityType = 'orderticket';
        foreach ($defaultStoreIds as $storeId) {
            $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
                ->setSuffix($this->sequenceConfig->get('suffix'))
                ->setStartValue($this->sequenceConfig->get('startValue'))
                ->setStoreId($storeId)
                ->setStep($this->sequenceConfig->get('step'))
                ->setWarningValue($this->sequenceConfig->get('warningValue'))
                ->setMaxValue($this->sequenceConfig->get('maxValue'))
                ->setEntityType($entityType)->create();

        }
    }


    /**
     * Add ORDERTICKET Item Attributes to Forms
     *
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Designnbuy\OrderTicket\Setup\OrderTicketSetup $installer
     *
     * @return void
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function installForms(ModuleDataSetupInterface $setup, OrderTicketSetup $installer)
    {
       
    }

}
