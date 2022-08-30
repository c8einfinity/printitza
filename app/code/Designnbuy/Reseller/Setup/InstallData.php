<?php
namespace Designnbuy\Reseller\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context) 
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        /*$eavSetup->removeAttribute(
          \Magento\Catalog\Model\Product::ENTITY,
            'product_commission');
        $eavSetup->removeAttribute(
          \Magento\Catalog\Model\Product::ENTITY,
            'commission_type');*/
            
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_commission',
            [
                'group' => 'General',
                'type' => 'decimal',
                'backend' => '',
                'frontend' => '',
                'label' => 'Product Commission',
                'input' => 'text',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
            ]);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'product_commission', 1);

        
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY, 'commission_type',
            [
                'type' => 'text',
                'backend' => '',
                'frontend' => '',
                'label' => 'Commission Type', 
                'input' => 'select',
                'source' => 'Designnbuy\Reseller\Model\Product\Attribute\Source\CommissionType',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE, 
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
            ]
        );
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'commission_type', 2);
    }
}