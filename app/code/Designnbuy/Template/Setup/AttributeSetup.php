<?php

namespace Designnbuy\Template\Setup;

use Designnbuy\Template\Model\Template\AttributeFactory;
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
     * @var TemplateFactory
     */
    private $attributeFactory;

    /**
     * Init
     *
     * @param ModuleDataSetupInterface $setup
     * @param Context $context
     * @param CacheInterface $cache
     * @param CollectionFactory $attrGroupCollectionFactory
     * @param TemplateFactory $templateFactory
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
            'designnbuy_template' => [
                'entity_model' => 'Designnbuy\Template\Model\ResourceModel\Template',
                'attribute_model' => 'Designnbuy\Template\Model\ResourceModel\Eav\Attribute',
                'table' => 'designnbuy_template_entity',
                'additional_attribute_table'    => 'designnbuy_template_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Template\Model\ResourceModel\Template\Attribute\Collection',
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
                        'type' => 'int',
                        'label' => 'Categories',
                        'input' => 'text',
                        'required' => true,
                        'position' => 2,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'group'          => 'General',
                        'visible'        => 0,
                        //'source' => 'Designnbuy\Template\Model\Category\Source',
                    ],
                    'image' => [
                        'group'          => 'General',
                        'type'           => 'varchar',
                        'backend'        => 'Designnbuy\Template\Model\Attribute\Backend\Image',
                        'label'          => 'Thumbnail Image',
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
                    'unit' => [
                        'group'          => 'Template Settings',
                        'type'           => 'varchar',
                        'label'          => 'Unit',
                        'input'          => 'select',
                        'source'         => 'Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => true,
                        'apply_to' => 'template,layout',
                        'position' => 9,
                    ],
                    'no_of_pages' => [
                        'group'          => 'Template Settings',
                        'type'           => 'varchar',
                        'label'          => 'No. of Pages',
                        'input'          => 'text',
                        'source'         => '',
                        'default'        => 1,
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => true,
                        'apply_to' => 'template,layout',
                        'position' => 10,
                        'frontend_class'=> 'validate-digits',
                    ],
                    'width' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Width',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => true,
                        'apply_to' => 'template,layout',
                        'position' => 11,
                        'frontend_class'=> 'validate-number',
                    ],
                    'height' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Height',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => true,
                        'apply_to' => 'template,layout',
                        'position' => 12,
                        'frontend_class'=> 'validate-number',
                    ],
                    'top_safe_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Top Safe Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'apply_to' => 'template,layout',
                        'required'       => false,
                        'position' => 13,
                        'frontend_class'=> 'validate-number',
                    ],
                    'right_safe_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Right Safe Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 14,
                        'frontend_class'=> 'validate-number',
                    ],
                    'bottom_safe_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Bottom Safe Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'position' => 15,
                        'frontend_class'=> 'validate-number',
                    ],
                    'left_safe_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Left Safe Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 16,
                        'frontend_class'=> 'validate-number',
                    ],
                    'top_bleed_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Top Bleed Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 17,
                        'frontend_class'=> 'validate-number',
                    ],
                    'right_bleed_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Right Bleed Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 18,
                        'frontend_class'=> 'validate-number',
                    ],
                    'bottom_bleed_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Bottom Bleed Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 19,
                        'frontend_class'=> 'validate-number',
                    ],
                    'left_bleed_margin' => [
                        'group'          => 'Template Settings',
                        'type'           => 'decimal',
                        'label'          => 'Left Bleed Margin',
                        'input'          => 'text',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                        'required'       => false,
                        'apply_to' => 'template,layout',
                        'position' => 20,
                        'frontend_class'=> 'validate-number',
                    ],
                    'svg' => [
                        'group'          => 'Template Settings',
                        'type'           => 'text',
                        'label'          => 'SVG',
                        'input'          => 'textarea',
                        'global'         => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'required'       => '0',
                        'position' => 21,
                        'visible'        => 0,
                    ]
                ],
            ],
            'designnbuy_template_category' => [
                'entity_model' => 'Designnbuy\Template\Model\ResourceModel\Category',
                'attribute_model' => 'Designnbuy\Template\Model\ResourceModel\Category\Eav\Attribute',
                'table' => 'designnbuy_template_category_entity',
                //'entity_attribute_collection' => 'Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection',
                'additional_attribute_table' => 'designnbuy_template_eav_attribute',
                'entity_attribute_collection' => 'Designnbuy\Template\Model\ResourceModel\Category\Attribute\Collection',
                'attributes' => [
                    'title' => [
                        'type' => 'varchar',
                        'label' => 'Name',
                        'input' => 'text',
                       // 'source' => 'Designnbuy\Template\Model\Product\Attribute\Source\Template',
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
                    'template_include_in_menu' => [
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
