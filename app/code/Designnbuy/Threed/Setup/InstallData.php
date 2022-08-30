<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Threed\Setup;

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
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_3d',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Enable 3D',
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
        $eavSetup->updateAttribute('catalog_product', 'is_3d', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'is_3d', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', '3D Preview Settings', 'is_3d', 1);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', '3D Preview Settings', 'is_3d', 1);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'model_3d',
            [
                'type' => 'varchar',
                'backend' => 'Designnbuy\Threed\Model\Product\Attribute\Backend\Threed',
                'frontend' => '',
                'label' => '3D Model',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
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
        $eavSetup->updateAttribute('catalog_product', 'model_3d', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'model_3d', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', '3D Preview Settings', 'model_3d', 2);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', '3D Preview Settings', 'model_3d', 2);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'map_image',
            [
                'type' => 'varchar',
                'backend' => 'Designnbuy\Threed\Model\Product\Attribute\Backend\Threed',
                'frontend' => '',
                'label' => 'Map Image',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
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
        $eavSetup->updateAttribute('catalog_product', 'map_image', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'map_image', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', '3D Preview Settings', 'map_image', 3);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', '3D Preview Settings', 'map_image', 3);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'threed_configure_area',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => '3D Configure Area',
                'input' => 'text',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => false,
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
        $eavSetup->updateAttribute('catalog_product', 'threed_configure_area', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'threed_configure_area', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', '3D Preview Settings', 'threed_configure_area', 4);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', '3D Preview Settings', 'threed_configure_area', 4);

        $setup->endSetup();
    }
}