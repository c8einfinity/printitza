<?php


namespace Designnbuy\Vendor\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
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

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();

        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $categorySetup->removeAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'vendor');
            $categorySetup->removeAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'vendor');

            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Vendor Settings');
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Vendor Settings', 'vendor_assignment', 1);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Vendor Settings', 'vendor_id', 2);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Vendor Settings', 'vendor_commission', 3);

            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Vendor Settings');
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Vendor Settings', 'vendor_assignment', 1);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Vendor Settings', 'vendor_id', 2);
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Vendor Settings', 'vendor_commission', 3);
        }

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $categorySetup->updateAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Vendor Settings', 'sort_order', 19);
        }
        $setup->endSetup();
    }
}
