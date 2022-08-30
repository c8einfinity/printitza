<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Merchandise\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Eav\Setup\EavSetupFactory; /* For Attribute create  */

class UpgradeData implements UpgradeDataInterface
{
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

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['inks' => $setup]);
        $entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);

        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            // allow_text attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_text')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_text', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_text',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Text',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_text', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_text', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_text', 40);
            }

            // allow_clipart attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_clipart')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_clipart', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_clipart',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Clipart',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_clipart', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_clipart', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_clipart', 50);
            }

            // allow_qr_code attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_qr_code')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_qr_code', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_qr_code',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable QR Code',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_qr_code', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_qr_code', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_qr_code', 50);
            }

            // allow_image_upload attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_image_upload')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_image_upload', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_image_upload',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Image Upload',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_image_upload', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_image_upload', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_image_upload', 50);
            }

            // allow_product attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_product')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_product', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_product',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Product Change',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_product', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_product', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_product', 40);
            }

            // allow_name_number attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_name_number')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_name_number', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_name_number',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable NameNumber',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_name_number', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_name_number', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_name_number', 40);
            }
        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            // allow_name_number attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_spot_color_output')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_spot_color_output', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_spot_color_output',
                    [
                        //'group' => 'CustomProduct Settings',
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Spot Color Output',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_spot_color_output', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_spot_color_output', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allow_spot_color_output', 100);
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings');
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'merchandise_personalize_option', 1);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_scratch', 2);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_product', 3);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_add_page', 4);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_text', 5);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_clipart', 6);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_qr_code', 7);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_image_upload', 8);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'allow_name_number', 9);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'element_color_picker_type', 10);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'color_category', 11);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Production Settings', 'allow_spot_color_output', 12);
        }

        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $categorySetup->updateAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'sort_order', 14);
        }

        $setup->endSetup();
    }
}
?>