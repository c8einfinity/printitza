<?php


namespace Designnbuy\Workflow\Setup;

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
            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Production Settings');
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomProduct', 'Production Settings', 'workflow_group', 1);


            $categorySetup->addAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Production Settings');
            $categorySetup->addAttributeToGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Production Settings', 'workflow_group', 1);

        }

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $categorySetup->updateAttributeGroup(\Magento\Catalog\Model\Product::ENTITY, 'CustomPrint', 'Production Settings', 'sort_order', 19);
        }
        $setup->endSetup();
    }
}
