<?php
namespace Designnbuy\Orderattachment\Setup;
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
    $eavSetup->addAttribute(
        \Magento\Catalog\Model\Product::ENTITY,
        'allowattachment',
        [
            'group' => 'General',
            'type' => 'int',
            'backend' => '',
            'frontend' => '',
            'label' => 'Allow Order Attachment',
            'input' => 'boolean',
            'class' => '',
            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
            'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            'visible' => true,
            'required' => false,
            'user_defined' => false,
            'default' => '0',
            'searchable' => false,
            'filterable' => false,
            'comparable' => false,
            'visible_on_front' => false,
            'used_in_product_listing' => false,
            'unique' => false,
            'apply_to' => 'simple,configurable,virtual,bundle,downloadable'
        ]);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomPrint', 'CanvasCanvas Settings', 'allowattachment', 70);
        $eavSetup->addAttributeToSet('catalog_product', 'CustomProduct', 'CustomProduct Settings', 'allowattachment', 70);
    }
}