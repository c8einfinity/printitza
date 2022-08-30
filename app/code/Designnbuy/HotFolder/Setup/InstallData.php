<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\HotFolder\Setup;

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

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $categorySetup = $this->categorySetupFactory->create(['inks' => $setup]);
        $attributeSet = $this->attributeSetFactory->create();
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $customCanvasAttributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomPrint');
        $customProductAttributeSetId = $categorySetup->getAttributeSetId($entityTypeId, 'CustomProduct');

        // Adding attribute group Canvas Settings to CustomCanvas atribute set
        $canvasSettingsTabName = 'CustomCanvas Settings';
        $categorySetup->addAttributeGroup($entityTypeId, $customCanvasAttributeSetId, 'CustomCanvas Settings', 70);
        $categorySetup->addAttributeGroup($entityTypeId, $customProductAttributeSetId, 'CustomProduct Settings', 70);
        // folder_location attribute

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'folder_location',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Folder Location',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\HotFolder\Model\Product\Attribute\Source\FolderLocation',
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
        $eavSetup->updateAttribute('catalog_product', 'folder_location', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'folder_location', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'HotFolder Settings', 'folder_location', 1);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'HotFolder Settings', 'folder_location', 1);

        // is_hotfolder_enable attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'is_hotfolder_enable',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Set Folder Naming Convention?',
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
                'note' => "Yes = Product Configuration, No = Global Configuration",
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'is_hotfolder_enable', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'is_hotfolder_enable', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'HotFolder Settings', 'is_hotfolder_enable', 2);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'HotFolder Settings', 'is_hotfolder_enable', 3);

        // prefix attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'prefix',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Prefix',
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
        $eavSetup->updateAttribute('catalog_product', 'prefix', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'prefix', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'HotFolder Settings', 'prefix', 3);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'HotFolder Settings', 'prefix', 3);

        // postfix attribute
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'postfix',
            [
                //'group' => 'CanvasCanvas Settings',
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Postfix',
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
        $eavSetup->updateAttribute('catalog_product', 'postfix', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'postfix', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'HotFolder Settings', 'postfix', 4);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'HotFolder Settings', 'postfix', 4);


        /* item_naming attribute */
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'item_naming',
            [
                //'group' => 'CustomProduct Settings',
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Naming String',
                'input' => 'select',
                'class' => '',
                'source' => 'Designnbuy\HotFolder\Model\Product\Attribute\Source\NamingString',
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
                'note' => "Yes = Folder name = Prefix_Naming String_Postfix",
            ]
        );
        $eavSetup->updateAttribute('catalog_product', 'item_naming', 'apply_to', 'simple,configurable');
        $eavSetup->updateAttribute('catalog_product', 'item_naming', 'is_configurable', 0);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'HotFolder Settings', 'item_naming', 5);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'HotFolder Settings', 'item_naming', 5);


    }
}