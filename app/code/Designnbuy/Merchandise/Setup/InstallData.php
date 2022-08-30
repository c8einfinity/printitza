<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Merchandise\Setup;

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

    /**
     * @var \Magento\Eav\Model\AttributeRepository
     * @param \Magento\Eav\Model\AttributeRepository $attributeRepository
     */
    protected $attributeRepository;
    
    public function __construct(
        AttributeSetFactory $attributeSetFactory,
        CategorySetupFactory $categorySetupFactory,
        EavSetupFactory $eavSetupFactory,
        \Magento\Eav\Model\AttributeRepository $attributeRepository
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->attributeRepository = $attributeRepository;
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
            'attribute_set_name' => 'CustomProduct', //attribute set name
            'entity_type_id' => $entityTypeId,
            'sort_order' => 50,
        ];

        $attributeSet->setData($data);
        $attributeSet->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($defaultAttributeSetId)->save(); // based on default attribute set

        $attributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomProduct');

        // Adding attribute group CustomProduct Settings to CustomProduct atribute set
        $productSettingsTabName = 'CustomProduct Settings';
        $categorySetup->addAttributeGroup($entityTypeId, $attributeSetId, $productSettingsTabName, 60);


        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        // is_customizable attribute
        $isCustomizable = $this->attributeRepository->get('catalog_product', 'is_customizable');
        if($isCustomizable->getAttributeId()){
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'is_customizable', 1);
        } else {
            // is_customizable attribute
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'is_customizable', /* Custom Attribute Code */
                [
                    //'group' => 'CustomProduct Settings',/* Group name in which you want to display your custom attribute */
                    'type' => 'int',/* Data type in which format your value save in database*/
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Customizable Product', /* label of your attribute*/
                    'input' => 'boolean',
                    'class' => '',
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
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'is_customizable', 1);
        }

        // base_unit attribute
        $baseUnit = $this->attributeRepository->get('catalog_product', 'base_unit');
        if($baseUnit->getAttributeId()){
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'base_unit', 2);
        } else {
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'base_unit');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'base_unit',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'text',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Unit for Measurement',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit',
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
            $eavSetup->updateAttribute('catalog_product', 'base_unit', 'apply_to', 'simple,configurable');
            $eavSetup->updateAttribute('catalog_product', 'base_unit', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'base_unit', 2);
        }

        // is_multicolor attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_multicolor',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Multicolor',
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
        $eavSetup->updateAttribute('catalog_product', 'is_multicolor', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'is_multicolor', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'is_multicolor', 3);

        /* pricing_logic attribute */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'pricing_logic',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Pricing Logic',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Merchandise\Model\Product\Attribute\Source\PricingLogic',
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
        $eavSetup->updateAttribute('catalog_product', 'pricing_logic', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'pricing_logic', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'pricing_logic', 4);

        /* element_color_picker_type attribute */
        $elementColorPickerType = $this->attributeRepository->get('catalog_product', 'element_color_picker_type');
        if($elementColorPickerType->getAttributeId()){
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'element_color_picker_type', 5);
        } else {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'element_color_picker_type',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Element Color Options',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Base\Model\Product\Attribute\Source\ElementColorPickerType',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => true,
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
            $eavSetup->updateAttribute('catalog_product', 'element_color_picker_type', 'apply_to', 'simple,configurable');
            $eavSetup->updateAttribute('catalog_product', 'element_color_picker_type', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'element_color_picker_type', 5);
        }

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'no_of_sides',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'No Of Sides',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Merchandise\Model\Product\Attribute\Source\NoOfSides',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => true,
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
        $eavSetup->updateAttribute('catalog_product', 'no_of_sides', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'no_of_sides', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'no_of_sides', 6);


        // sides_configuration attribute
        $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sides_configuration');
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'sides_configuration',
            [
                'type' => 'varchar',
                'backend' => 'Designnbuy\Merchandise\Model\Product\Attribute\Backend\Side',
                'frontend' => '',
                'label' => 'Side Configuration',
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
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'sides_configuration', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'sides_configuration', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Sides Configuration', 'sides_configuration', 1);

        // merchandise_personalize_option attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'merchandise_personalize_option',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Personalization Options',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\Merchandise\Model\Product\Attribute\Source\PersonalizationOptions',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
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
        $eavSetup->updateAttribute('catalog_product', 'merchandise_personalize_option', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'merchandise_personalize_option', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'merchandise_personalize_option', 1);

        // designidea_category attribute

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'designidea_category',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => ' DesignIdea Category',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Designidea\Model\Category\Source',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
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
            $eavSetup->updateAttribute('catalog_product', 'designidea_category', 'apply_to', 'simple,configurable');
            $eavSetup->updateAttribute('catalog_product', 'designidea_category', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Merchandise Personalisation', 'designidea_category', 2);


        // designidea_id attribute

            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'designidea_id',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => ' DesignIdea',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Designidea\Model\Designidea\Source',
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
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
            $eavSetup->updateAttribute('catalog_product', 'designidea_id', 'apply_to', 'simple,configurable');
            $eavSetup->updateAttribute('catalog_product', 'designidea_id', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Merchandise Personalisation', 'designidea_id', 3);

    }
}