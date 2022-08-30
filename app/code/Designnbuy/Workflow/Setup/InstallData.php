<?php


namespace Designnbuy\Workflow\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory; /* For Attribute create  */

use Magento\Framework\DB\Ddl\Table;

class InstallData implements InstallDataInterface {

    private $attributeSetFactory;

    private $categorySetupFactory;

    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     */
    protected $attributeRepository;

    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\AttributeRepository $attributeRepository,
        \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory,
        \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // workflow_group attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'workflow_group',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Workflow Group',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Workflow\Model\Product\Attribute\Source\WorkFlowGroup',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'workflow_group', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'workflow_group', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'workflow_group', 25);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'workflow_group', 25);

        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $quoteInstaller->addAttribute(
            'quote_item',
            'workflow_status',
            ['type' => Table::TYPE_SMALLINT]
        );
        $salesInstaller->addAttribute(
            'order_item',
            'workflow_status',
            ['type' => Table::TYPE_SMALLINT]
        );

        $setup->endSetup();
    }
}
