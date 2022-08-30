<?php
namespace Designnbuy\Book\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;


class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;
    private $attributeSetFactory;
    private $attributeSet;
    private $categorySetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory, AttributeSetFactory $attributeSetFactory, CategorySetupFactory $categorySetupFactory )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        // CREATE ATTRIBUTE SET
        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
        $data = [
            'attribute_set_name' => 'CustomBook',
            'entity_type_id' => $entityTypeId,
            'sort_order' => 200,
        ];
        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($attributeSetId);
        $attributeSet->save();

        // Adding attribute group Canvas Settings to CustomPrint atribute set
        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomBook');
        $bookSettingsTabName = 'Book Settings';
        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, $bookSettingsTabName, 60);

        // CREATE PRODUCT ATTRIBUTE
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'additional_service_cost',
            [
                'type' => 'varchar',
                'label' => 'Additional Service Cost',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'attribute_set_id' => 'CustomBook',
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomBook', 'Book Settings', 'additional_service_cost', 10);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'packing',
            [
                'type' => 'varchar',
                'label' => 'Packing',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'attribute_set_id' => 'CustomBook',
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomBook', 'Book Settings', 'packing', 20);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'courier_cost',
            [
                'type' => 'varchar',
                'label' => 'Courier Cost',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'attribute_set_id' => 'CustomBook',
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomBook', 'Book Settings', 'courier_cost', 30);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'distance_km',
            [
                'type' => 'varchar',
                'label' => 'Distance KM',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'attribute_set_id' => 'CustomBook',
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomBook', 'Book Settings', 'distance_km', 40);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'cover_printing_cost',
            [
                'type' => 'varchar',
                'label' => 'Cover Printing Cost',
                'backend' => '',
                'input' => 'text',
                'wysiwyg_enabled'   => false,
                'source' => '',
                'required' => false,
                'sort_order' => 5,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
                'used_in_product_listing' => true,
                'visible_on_front' => true,
                'attribute_set_id' => 'CustomBook',
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomBook', 'Book Settings', 'cover_printing_cost', 50);

        $setup->endSetup();
    }

} ?>