<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory; /* For Attribute create  */

class InstallData implements InstallDataInterface {

    private $attributeSetFactory;

    private $categorySetupFactory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['inks' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $defaultAttributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId); // Default attribute set Id
        $data = [
            'attribute_set_name' => 'CustomPrint', //attribute set name
            'entity_type_id' => $entityTypeId,
            'sort_order' => 50,
        ];
        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($defaultAttributeSetId)->save(); // based on default attribute set
        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomPrint');

        // Adding attribute group Canvas Settings to CustomPrint atribute set
        $canvasSettingsTabName = 'CustomPrint Settings';
        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, $canvasSettingsTabName, 60);

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /* is_customizable attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_customizable');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_customizable', /* Custom Attribute Code */
            [
                //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                'type' => 'int',/* Data type in which format your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Customizable Product', /* label of your attribute*/
                'input' => 'boolean',
                'class' => '',
                //'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean', /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'is_customizable', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'is_customizable', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'is_customizable', 1);


        /* base_unit attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'base_unit');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'base_unit', /* Custom Attribute Code */
            [
                //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                'type' => 'text',/* Data type in which format your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Unit for Measurement', /* label of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit', /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
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
        $eavSetup->updateAttribute('catalog_product', 'base_unit', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'base_unit', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'base_unit', 2);

        // width attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'width');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'width',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Width',
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
                'apply_to' => 'simple',
                'note' => "Width will be used in case of product has no size custom option.",
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'width', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'width', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'width', 3);

        // height attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'height');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'height',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Height',
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
                'apply_to' => 'simple',
                'note' => "Height will be used in case of product has no size custom option.",
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'height', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'height', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'height', 3);

        // top_bleed_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'top_bleed_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'top_bleed_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Top Bleed Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'top_bleed_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'top_bleed_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'top_bleed_margin', 4);

        // right_bleed_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'right_bleed_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'right_bleed_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Right Bleed Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'right_bleed_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'right_bleed_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'right_bleed_margin', 5);

        // bottom_bleed_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'bottom_bleed_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'bottom_bleed_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Bottom Bleed Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'bottom_bleed_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'bottom_bleed_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'bottom_bleed_margin', 6);

        // left_bleed_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'left_bleed_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'left_bleed_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Left Bleed Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'left_bleed_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'left_bleed_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'left_bleed_margin', 7);

        ///////
        // top_safe_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'top_safe_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'top_safe_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Top Safe Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'top_safe_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'top_safe_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'top_safe_margin', 8);

        // right_safe_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'right_safe_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'right_safe_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Right Safe Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'right_safe_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'right_safe_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'right_safe_margin', 9);

        // bottom_safe_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'bottom_safe_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'bottom_safe_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Bottom Safe Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'bottom_safe_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'bottom_safe_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'bottom_safe_margin', 10);

        // left_safe_margin attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'left_safe_margin');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'left_safe_margin',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Left Safe Margin',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'left_safe_margin', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'left_safe_margin', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'left_safe_margin', 11);

        // no_of_pages attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'no_of_pages');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'no_of_pages',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'No. of Sides',
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
                'apply_to' => 'simple',
                'note' => "Number of sides will be used in case of product has no sides custom option.",
                'frontend_class' => 'validate-digits'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'no_of_pages', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'no_of_pages', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'no_of_pages', 12);

        // corner_radius attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'corner_radius');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'corner_radius',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Corner Radius',
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
                'apply_to' => 'simple',
                'frontend_class' => 'validate-number'
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'corner_radius', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'corner_radius', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'corner_radius', 13);

        /* allow_scratch attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_scratch');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_scratch',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Start from Scratch',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_scratch', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'allow_scratch', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_scratch', 14);

        /* allow_background_image attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_background_image');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_background_image',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Background Image',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_background_image', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'allow_background_image', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_background_image', 15);

        /* allow_background_color attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_background_color');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_background_color',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Background Colors',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_background_color', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'allow_background_color', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_background_color', 16);

        /* allow_vdp attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_vdp');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_vdp',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable VDP',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_vdp', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'allow_vdp', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_vdp', 17);

        /* allow_border attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_border');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_border',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Border',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 1,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_border', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'allow_border', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_border', 18);

        /* allow_border attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'allow_border');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'allow_border',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable Border',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 1,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'allow_border', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'allow_border', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allow_border', 19);

        /* bg_color_picker_type attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'bg_color_picker_type');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'bg_color_picker_type', /* Custom Attribute Code */
            [
                //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                'type' => 'int',/* Data type in which format your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Background Color Options', /* label of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Canvas\Model\Product\Attribute\Source\BackgroundColorPickerType', /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
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
                'note' => 'Set canvas backgound color options as full RGB color picker or admin defined printable colors. Global setting, applicable if product specific setting is not defined.',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'bg_color_picker_type', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'bg_color_picker_type', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'bg_color_picker_type', 20);

        /* element_color_picker_type attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'element_color_picker_type');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'element_color_picker_type', /* Custom Attribute Code */
            [
                //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                'type' => 'int',/* Data type in which format your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Element Color Options', /* label of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Base\Model\Product\Attribute\Source\ElementColorPickerType', /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
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
                'note' => 'Set clipart, text, shape etc. decoration color options as full RGB color picker or admin defined printable colors. Global setting, applicable if product specific setting is not defined.',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'element_color_picker_type', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'element_color_picker_type', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'element_color_picker_type', 20);



        /* is_double_page attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'is_double_page');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_double_page',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Follow Book Display Format?',
                'input' => 'boolean',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
                'user_defined' => false,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => 'simple',
                'note'          => 'Display page numbering in book format (2-3/10) while admin has to manage two pages within same page using overlay to define page line.',
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'is_double_page', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'is_double_page', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'is_double_page', 22);


        /* canvas_personalize_option attribute */
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'canvas_personalize_option');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'canvas_personalize_option', /* Custom Attribute Code */
            [
                //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                'type' => 'int',/* Data type in which format your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => 'Personalization Options', /* label of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Canvas\Model\Product\Attribute\Source\PersonalizationOptions', /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
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
        $eavSetup->updateAttribute('catalog_product', 'canvas_personalize_option', 'apply_to', 'simple');
        $eavSetup->updateAttribute('catalog_product', 'canvas_personalize_option', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'canvas_personalize_option', 21);


        // template_category attribute

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'template_category',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => ' Template Category',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Template\Model\Category\Source',
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
            $eavSetup->updateAttribute('catalog_product', 'template_category', 'apply_to', 'simple');
            $eavSetup->updateAttribute('catalog_product', 'template_category', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Canvas Personalisation', 'template_category', 1);


        // template_id attribute

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'template_id',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => ' Template',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Template\Model\Template\Source',
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
            $eavSetup->updateAttribute('catalog_product', 'template_id', 'apply_to', 'simple');
            $eavSetup->updateAttribute('catalog_product', 'template_id', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Canvas Personalisation', 'template_id', 1);

    }
}