<?php
namespace Designnbuy\Pricecalculator\Setup;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, CategorySetupFactory $categorySetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) 
    {
        $categorySetup = $this->categorySetupFactory->create(['inks' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomPrint');

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        // Adding attribute group Advanced Custom Width Height Pricing to CustomPrint atribute set
        $canvasSettingsTabName = 'Advanced Custom Width Height Pricing';
        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, $canvasSettingsTabName, 70);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'pricing_limit',
            [
                'group' => 'General',
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Custom Height/Width limit',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'note' => 'For applying limit on custom options please use the following syntex Height_min=5;Height_max=5;Width_min=10;Width_max=10; this will set upper and lower limit for each field Please use keyword given in configuration like keyword min or keyword max.',
                'unique' => false,
                'apply_to' => 'simple'
            ]);
        $eavSetup->updateAttribute('catalog_product', 'pricing_limit', 'apply_to', 'simple');
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Advanced Custom Width Height Pricing', 'pricing_limit', 1);

        $eavSetup->addAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'square_area_price',
        [
            'group' => 'General',
            'type' => 'text',
            'backend' => 'Designnbuy\Pricecalculator\Model\Product\Attribute\Backend\SquarePrice',
            'frontend' => '',
            'label' => 'Square Area Price',
            'input' => 'text',
            'class' => '',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,            
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'note' => 'Enter Value Greater Then Zero',
            'unique' => false,
            'apply_to' => 'simple'
        ]);
        $eavSetup->updateAttribute('catalog_product', 'square_area_price', 'apply_to', 'simple');
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Advanced Custom Width Height Pricing', 'square_area_price', 2);


        $eavSetup->addAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'enable_custom_height_width',
        [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Enable Custom Height/Width',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '0',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => 'simple'
        ]);
        $eavSetup->updateAttribute('catalog_product', 'enable_custom_height_width', 'apply_to', 'simple');
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'enable_custom_height_width', 50);

    }
}