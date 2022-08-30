<?php

namespace Designnbuy\Designidea\Setup;

use Designnbuy\Designidea\Model\Designidea\AttributeFactory;
use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class AttributeSetup extends EavSetup
{
    /**
     * Category model factory
     *
     * @var DesignideaFactory
     */
    private $attributeFactory;

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
        AttributeFactory $attributeFactory
    ) {
        $this->attributeFactory = $attributeFactory;
        parent::__construct($setup, $context, $cache, $attrGroupCollectionFactory);
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
                'additional_attribute_table'    => 'designnbuy_designidea_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Designidea\Model\ResourceModel\Designidea\Attribute\Collection',
                'attributes' => [
                    'title' => [
                        'type' => 'varchar',
                        'label' => 'Name',
                        'input' => 'text',
                        'frontend_class' => 'validate-length maximum-length-255',
                        'position' => 1,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'General',
                    ],
                    'category_id' => [
                        'type' => 'varchar',
                        'label' => 'Categories',
                        'input' => 'text',
                        'required' => true,
                        'position' => 2,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'General',
                        'visible'        => 1,
                        //'source' => 'Designnbuy\Designidea\Model\Category\Source',
                    ],
                    'image' => [
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => 'Designnbuy\Designidea\Model\Attribute\Backend\Image',
                        'label'          => 'Thumbnail Image',
                        'input'          => 'image',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => false,
                        'position'       => 3,
                    ],
                    'preview_image' => [
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => 'Designnbuy\Designidea\Model\Attribute\Backend\Image',
                        'label'          => 'Preview Thumbnail Image',
                        'input'          => 'image',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => false,
                        'position'       => 3,
                    ],
                    'status' => [
                        'type' => 'int',
                        'label' => 'Enabled',
                        'input' => 'boolean',
                        'required' => true,
                        'position' => 4,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'General',
                    ],
                    'position' => [
                        'group'          => 'General',
                        'type'           => 'int',
                        'label'          => 'Position',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'position' => 4,
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Description',
                        'input' => 'textarea',
                        'required' => false,
                        'position' => 5,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'wysiwyg_enabled' => true,
                        'is_html_allowed_on_front' => true,
                        'group'          => 'Content',
                    ],
                    'identifier' => [
                        'type' => 'varchar',
                        'label' => 'URL Key',
                        'input' => 'text',
                        'required' => false,
                        'position' => 6,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'Search Engine Optimization',
                    ],
                    'meta_title' => [
                        'type' => 'varchar',
                        'label' => 'Meta Title',
                        'input' => 'text',
                        'required' => false,
                        'position' => 7,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'Search Engine Optimization',
                    ],
                    'meta_description' => [
                        'type' => 'varchar',
                        'label' => 'Meta Description',
                        'input' => 'textarea',
                        'required' => false,
                        'note' => 'Maximum 255 chars',
                        'class' => 'validate-length maximum-length-255',
                        'position' => 8,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'Search Engine Optimization',
                    ],
                    'svg' => [
                        'group'          => 'Design Tool Settings',
                        'type'           => 'text',
                        'label'          => 'SVG',
                        'input'          => 'textarea',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => '0',
                        'position' => 21,
                        'visible'        => 0,
                    ],
                    'options' => [
                        'group'          => 'Design Tool Settings',
                        'type'           => 'text',
                        'label'          => 'Options',
                        'input'          => 'textarea',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => '0',
                        'position' => 21,
                        'visible'        => 0,
                    ],
                    'product_id' => [
                        'group'          => 'Design Tool Settings',
                        'type'           => 'int',
                        'label'          => 'Product ID',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => '0',
                        'position' => 21,
                        'visible'        => 0,
                    ]
                ],
            ],
            'designnbuy_designidea_category' => [
                'entity_model' => 'Designnbuy\Designidea\Model\ResourceModel\Category',
                'attribute_model' => 'Designnbuy\Designidea\Model\ResourceModel\Category\Eav\Attribute',
                'table' => 'designnbuy_designidea_category_entity',
                //'entity_attribute_collection' => 'Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection',
                'additional_attribute_table' => 'designnbuy_designidea_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Designidea\Model\ResourceModel\Category\Attribute\Collection',
                'attributes' => [
                    'title' => [
                        'type' => 'varchar',
                        'label' => 'Name',
                        'input' => 'text',
                        // 'source' => 'Designnbuy\Designidea\Model\Product\Attribute\Source\Designidea',
                        'frontend_class' => 'validate-length maximum-length-255',
                        'position' => 1,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'description' => [
                        'type' => 'text',
                        'label' => 'Description',
                        'input' => 'textarea',
                        'required' => false,
                        'position' => 2,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'wysiwyg_enabled' => true,
                        'is_html_allowed_on_front' => true,
                    ],
                    'status' => [
                        'type' => 'int',
                        'label' => 'Enabled',
                        'input' => 'boolean',
                        'required' => true,
                        'position' => 3,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    ],
                    'designidea_include_in_menu' => [
                        'type' => 'int',
                        'label' => 'Include in Menu',
                        'input' => 'boolean',
                        'required' => false,
                        'position' => 3,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    ],
                    'identifier' => [
                        'type' => 'varchar',
                        'label' => 'URL Key',
                        'input' => 'text',
                        'required' => false,
                        'position' => 3,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group' => 'General Information',
                    ],
                    'meta_title' => [
                        'type' => 'varchar',
                        'label' => 'Meta Title',
                        'input' => 'text',
                        'required' => false,
                        'position' => 10,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'meta_description' => [
                        'type' => 'varchar',
                        'label' => 'Meta Description',
                        'input' => 'textarea',
                        'required' => false,
                        'note' => 'Maximum 255 chars',
                        'class' => 'validate-length maximum-length-255',
                        'position' => 20,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    'path' => [
                        'type' => 'varchar',
                        'label' => 'Parent Category',
                        'input' => 'text',
                        'frontend_class' => '',
                        'position' => 21,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    ],
                    /*'include_in_menu' => [
                        'type' => 'int',
                        'label' => 'Include In Menu',
                        'input' => 'boolean',
                        'required' => false,
                        'position' => 22,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                    ],*/
                ],
            ]
        ];
    }
}
