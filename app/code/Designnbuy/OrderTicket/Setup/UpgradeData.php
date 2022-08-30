<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Sales setup factory
     *
     * @var OrderTicketSetupFactory
     */
    protected $orderticketSetupFactory;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @param OrderTicketSetupFactory $orderticketSetupFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        OrderTicketSetupFactory $orderticketSetupFactory,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->orderticketSetupFactory = $orderticketSetupFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        /** @var OrderTicketSetupFactory orderticketSetupFactory */
        $this->orderticketSetupFactory->create(['setup' => $setup]);
        $setup->endSetup();
    }
}
