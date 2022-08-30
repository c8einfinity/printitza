<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Setup;

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

        if (version_compare($context->getVersion(), '2.5.2', '<')) {
            // allow_text attribute
            $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'background_color_category');
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'background_color_category', /* Custom Attribute Code */
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Background Color Category',
                    'input' => 'select',
                    'class' => '',
                    'source' => 'Designnbuy\Color\Model\Product\Attribute\Source\Category',
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
            $eavSetup->updateAttribute('catalog_product', 'background_color_category', 'apply_to', 'simple,configurable');
            $eavSetup->updateAttribute('catalog_product', 'background_color_category', 'is_configurable', 0);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'Design Studio Settings', 'background_color_category', 18);
            $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'Design Studio Settings', 'background_color_category', 15);
            
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Design Studio Settings', 'background_color_category', 18);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Design Studio Settings', 'background_color_category', 15);
        }

        $setup->endSetup();
    }
}
?>