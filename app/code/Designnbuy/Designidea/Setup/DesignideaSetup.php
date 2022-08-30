<?php

namespace Designnbuy\Designidea\Setup;

use Designnbuy\Designidea\Model\DesignideaFactory;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class DesignideaSetup extends EavSetup
{
    /**
     * Category model factory
     *
     * @var DesignideaFactory
     */
    private $designideaFactory;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param DesignideaFactory $designideaFactory
     */
    public function __construct(
        ModuleDataSetupInterface $setup,
        Context $context,
        CacheInterface $cache,
        CollectionFactory $attrGroupCollectionFactory,
        DesignideaFactory $designideaFactory
    ) {
        $this->designideaFactory = $designideaFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
    }

    /**
     * Creates designidea model
     *
     * @param array $data
     * @return \Designnbuy\Designidea\Model\Designidea
     * @codeCoverageIgnore
     */
    public function createCategory($data = [])
    {
        return $this->designideaFactory->create($data);
    }

    /**
     * Default entities and attributes
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        return [
            'designnbuy_designidea' => [
                'entity_model' => 'Designnbuy\Designidea\Model\ResourceModel\Designidea',
                'attribute_model' => 'Designnbuy\Designidea\Model\ResourceModel\Eav\Attribute',
                'table' => 'designnbuy_designidea_entity',
                //'entity_attribute_collection' => 'Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection',
                'additional_attribute_table' => 'designnbuy_designidea_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Designidea\Model\ResourceModel\Designidea\Attribute\Collection',
                'attributes' => [
                    'name' => [
                        'type' => 'varchar',
                        'label' => 'Name',
                        'input' => 'text',
                        'source' => 'Designnbuy\Designidea\Model\Product\Attribute\Source\Designidea',
                        'frontend_class' => 'validate-length maximum-length-255',
                        'sort_order' => 1,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Description',
                        'input' => 'textarea',
                        'required' => false,
                        'sort_order' => 2,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'wysiwyg_enabled' => true,
                        'is_html_allowed_on_front' => true,
                    ],
                    'url_key' => [
                        'type' => 'varchar',
                        'label' => 'URL Key',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 3,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    'meta_title' => [
                        'type' => 'varchar',
                        'label' => 'Meta Title',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_description' => [
                        'type' => 'varchar',
                        'label' => 'Meta Description',
                        'input' => 'textarea',
                        'required' => false,
                        'note' => 'Maximum 255 chars',
                        'class' => 'validate-length maximum-length-255',
                        'sort_order' => 20,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                ],
            ],
            'designnbuy_designidea_category' => [
                'entity_model' => 'Designnbuy\Designidea\Model\ResourceModel\Category',
                'attribute_model' => 'Designnbuy\Designidea\Model\ResourceModel\Eav\Attribute',
                'table' => 'designnbuy_designidea_category_entity',
                //'entity_attribute_collection' => 'Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection',
                'additional_attribute_table' => 'designnbuy_designidea_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Designidea\Model\ResourceModel\Category\Attribute\Collection',
                'attributes' => [
                    'name' => [
                        'type' => 'varchar',
                        'label' => 'Name',
                        'input' => 'text',
                        'source' => 'Designnbuy\Designidea\Model\Product\Attribute\Source\Designidea',
                        'frontend_class' => 'validate-length maximum-length-255',
                        'sort_order' => 1,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Description',
                        'input' => 'textarea',
                        'required' => false,
                        'sort_order' => 2,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'wysiwyg_enabled' => true,
                        'is_html_allowed_on_front' => true,
                    ],
                    'url_key' => [
                        'type' => 'varchar',
                        'label' => 'URL Key',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 3,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    'meta_title' => [
                        'type' => 'varchar',
                        'label' => 'Meta Title',
                        'input' => 'text',
                        'required' => false,
                        'sort_order' => 10,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_description' => [
                        'type' => 'varchar',
                        'label' => 'Meta Description',
                        'input' => 'textarea',
                        'required' => false,
                        'note' => 'Maximum 255 chars',
                        'class' => 'validate-length maximum-length-255',
                        'sort_order' => 20,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                ],
            ],
        ];
    }
}
