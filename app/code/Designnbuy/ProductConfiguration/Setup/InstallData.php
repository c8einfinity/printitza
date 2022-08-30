<?php
namespace Designnbuy\ProductConfiguration\Setup;

use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
	/**
	 * @param GroupFactory $groupFactory 
	 */
	public function __construct(
		EavSetupFactory $eavSetupFactory
	) {
		$this->eavSetupFactory = $eavSetupFactory;
	}

	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
		 
			$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'hide_addtocart');
 			$eavSetup->addAttribute(
 			\Magento\Catalog\Model\Product::ENTITY,
 			'hide_addtocart',
				array(
					'type'                          => 'int',
					'input'                         => 'boolean',
					'default'                       => 0,
					'label'                         => 'Hide AddToCart',
					'backend'                       => '',
					'frontend'                      => '',
					'source'                        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
					'visible'                       => 1,
					'required'                      => 0,
					'user_defined'                  => 1,
					'used_for_price_rules'          => 1,
					'position'                      => 2,
					'unique'                        => 0,
					'sort_order'                    => 100,
					'is_global'                     => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
					'is_required'                   => 0,
					'is_configurable'               => 1,
					'is_searchable'                 => 0,
					'is_visible_in_advanced_search' => 0,
					'is_comparable'                 => 0,
					'is_filterable'                 => 0,
					'is_filterable_in_search'       => 1,
					'is_used_for_promo_rules'       => 1,
					'is_html_allowed_on_front'      => 0,
					'is_visible_on_front'           => 1,
					'used_in_product_listing'       => 1,
					'used_for_sort_by'              => 0,
					'apply_to' 						=> '',
				)
			);
		$attribute = $eavSetup->getAttribute('catalog_product', 'hide_addtocart');
		$eavSetup->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute['attribute_id']);
		$eavSetup->addAttributeToGroup('catalog_product', 'CustomProduct', 'General', $attribute['attribute_id']);
		$eavSetup->addAttributeToGroup('catalog_product', 'CustomPrint', 'General', $attribute['attribute_id']);

		$eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'hide_price');
 			$eavSetup->addAttribute(
 			\Magento\Catalog\Model\Product::ENTITY,
 			'hide_price',
				array(
					'type'                          => 'int',
					'input'                         => 'boolean',
					'default'                       => 0,
					'label'                         => 'Hide Price',
					'backend'                       => '',
					'frontend'                      => '',
					'source'                        => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
					'visible'                       => 1,
					'required'                      => 0,
					'user_defined'                  => 1,
					'used_for_price_rules'          => 1,
					'position'                      => 2,
					'unique'                        => 0,
					'sort_order'                    => 100,
					'is_global'                     => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE,
					'is_required'                   => 0,
					'is_configurable'               => 1,
					'is_searchable'                 => 0,
					'is_visible_in_advanced_search' => 0,
					'is_comparable'                 => 0,
					'is_filterable'                 => 0,
					'is_filterable_in_search'       => 1,
					'is_used_for_promo_rules'       => 1,
					'is_html_allowed_on_front'      => 0,
					'is_visible_on_front'           => 1,
					'used_in_product_listing'       => 1,
					'used_for_sort_by'              => 0,
					'apply_to' 						=> '',
				)
			);
		$attribute = $eavSetup->getAttribute('catalog_product', 'hide_price');
		$eavSetup->addAttributeToGroup('catalog_product', 'Default', 'General', $attribute['attribute_id']);
		$eavSetup->addAttributeToGroup('catalog_product', 'CustomProduct', 'General', $attribute['attribute_id']);
		$eavSetup->addAttributeToGroup('catalog_product', 'CustomPrint', 'General', $attribute['attribute_id']);
             
	}
}
