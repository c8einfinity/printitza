<?php


namespace Designnbuy\Vendor\Setup;

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

        // vendor attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'vendor_id',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Vendor',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Vendor\Model\Product\Attribute\Source\Vendor',
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
        $eavSetup->updateAttribute('catalog_product', 'vendor_id', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'vendor_id', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Vendor', 'vendor_id', 1);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Vendor', 'vendor_id', 1);


        // vendor_commission attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'vendor_commission');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'vendor_commission',
            [
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Commission',
                'input' => 'text',
                'class' => '',
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
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'vendor_commission', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'vendor_commission', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Vendor', 'vendor_commission', 2);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Vendor', 'vendor_commission', 2);


        // vendor_assignment attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'vendor_assignment',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Vendor Assignment',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Vendor\Model\Product\Attribute\Source\Assignment',
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
        $eavSetup->updateAttribute('catalog_product', 'vendor_assignment', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'vendor_assignment', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'vendor_assignment', 1);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'vendor_assignment', 1);

        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);
        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $quoteInstaller->addAttribute(
            'quote_item',
            'vendor_id',
            ['type' => Table::TYPE_SMALLINT]
        );
        $salesInstaller->addAttribute(
            'order_item',
            'vendor_id',
            ['type' => Table::TYPE_SMALLINT]
        );

        $salesInstaller->addAttribute(
            'order_item',
            'vendor_commission',
            ['type' => Table::TYPE_DECIMAL]
        );

        $setup->endSetup();
    }
}
