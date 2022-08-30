<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Sheet\Setup;

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

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Imposition Settings', 21);

        if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_nup_output')) {
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'allow_nup_output', 1);
        } else {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'allow_nup_output',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Enable N-Up Output',
                    'input' => 'boolean',
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
            $eavSetup->updateAttribute('catalog_product', 'allow_nup_output', 'apply_to', 'simple');
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'allow_nup_output', 100);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Imposition Settings', 'allow_nup_output', 1);
        }

        if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'nup_sheet_size')) {
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'nup_sheet_size', 2);
        } else {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'nup_sheet_size',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Sheet Size',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Sheet\Model\Product\Attribute\Source\Size', /* Source of your select type custom attribute options*/
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                    'visible' => true,
                    'required' => false,
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
            $eavSetup->updateAttribute('catalog_product', 'nup_sheet_size', 'apply_to', 'simple');
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'nup_sheet_size', 200);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Imposition Settings', 'nup_sheet_size', 2);
        }

        if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'nup_bleed_margin')) {
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'nup_bleed_margin', 3);
        } else {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'nup_bleed_margin',
                [
                    //'group' => 'CustomProduct Settings',
                    'type' => 'decimal',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Bleed Margin',
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
            $eavSetup->updateAttribute('catalog_product', 'nup_bleed_margin', 'apply_to', 'simple');
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Imposition Settings', 'nup_bleed_margin', 300);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Imposition Settings', 'nup_bleed_margin', 3);
        }



        $setup->endSetup();

    }
}