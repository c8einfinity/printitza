<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Canvas\Setup;

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
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_text', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_text',
                    [
                        //'group' => 'CustomPrint Settings',
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
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_text', 40);
            }

            // allow_clipart attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_clipart')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_clipart', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_clipart',
                    [
                        //'group' => 'CustomPrint Settings',
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
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_clipart', 50);
            }

            // allow_qr_code attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_qr_code')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_qr_code', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_qr_code',
                    [
                        //'group' => 'CustomPrint Settings',
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
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_qr_code', 50);
            }

            // allow_image_upload attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_image_upload')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_image_upload', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_image_upload',
                    [
                        //'group' => 'CustomPrint Settings',
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
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_image_upload', 50);
            }

            // allow_add_page attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_add_page')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_add_page', 1);
            } else {
                $eavSetup->addAttribute(
                    \Magento\Catalog\Model\Product::ENTITY, 'allow_add_page',
                    [
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Enable Add Page',
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
                $eavSetup->updateAttribute('catalog_product', 'allow_add_page', 'apply_to', 'simple');
                $eavSetup->updateAttribute('catalog_product', 'allow_add_page', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_add_page', 60);
            }

        }

        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            // allow_name_number attribute
            if($categorySetup->getAttributeId(\Magento\Catalog\Model\Product::ENTITY, 'allow_spot_color_output')) {
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_spot_color_output', 1);
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
                $eavSetup->updateAttribute('catalog_product', 'allow_spot_color_output', 'apply_to', 'simple,configurable');
                $eavSetup->updateAttribute('catalog_product', 'allow_spot_color_output', 'is_configurable', 0);
                $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CustomCanvas Settings', 'allow_spot_color_output', 100);
            }
        }

        if (version_compare($context->getVersion(), '1.0.3', '<')) {
            $categorySetup->updateAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'CustomCanvas Settings', 'sort_order', 13);
            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 14);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'canvas_personalize_option', 1);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_scratch', 2);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_border', 3);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_background_image', 4);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_background_color', 5);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_vdp', 6);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_add_page', 7);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_text', 8);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_clipart', 9);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_qr_code', 10);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'allow_image_upload', 11);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'is_double_page', 12);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'bg_color_picker_type', 13);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'element_color_picker_type', 14);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'color_category', 15);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Production Settings', 'allow_spot_color_output', 2);

        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            $categorySetup->updateAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'sort_order', 14);
        }
        
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'single_color_hex_code');

            $data = array(
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Color Code',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'wysiwyg_enabled'   => false,
                'source' => '',
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'sort_order' => 18,
                'default' => '',
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'note' => 'Example : #000000',
            );
             
             $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                'single_color_hex_code',
                $data
            );

            $eavSetup->updateAttribute('catalog_product', 'single_color_hex_code', 'apply_to', 'simple');
            $eavSetup->updateAttribute('catalog_product', 'single_color_hex_code', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Design Studio Settings', 'single_color_hex_code', 18);

            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'element_color_settings');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'element_color_settings', /* Custom Attribute Code */
                [
                    //'group' => 'CanvasCanvas Settings',/* Group name in which you want to display your custom attribute */
                    'type' => 'int',/* Data type in which format your value save in database*/
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Element Color Settings', /* label of your attribute*/
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Canvas\Model\Config\Source\ElementColorPickerSetting', /* Source of your select type custom attribute options*/
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL, /*Scope of your attribute */
                    'visible' => true,
                    'required' => false,
                    'user_defined' => false,
                    'sort_order' => 17,
                    'default' => 1,
                    'searchable' => false,
                    'filterable' => false,
                    'comparable' => false,
                    'visible_on_front' => false,
                    'used_in_product_listing' => true,
                    'unique' => false,
                    'apply_to' => 'simple',
                    //'note' => 'Set clipart, text, shape etc. decoration color options as full RGB color picker or admin defined printable colors. Global setting, applicable if product specific setting is not defined.',
                ]
            );
            $eavSetup->updateAttribute('catalog_product', 'element_color_settings', 'apply_to', 'simple');
            $eavSetup->updateAttribute('catalog_product', 'element_color_settings', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Design Studio Settings', 'element_color_settings', 17);
        }

        $setup->endSetup();
    }
}
?>