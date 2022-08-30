<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Designnbuy\Reseller\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class Reseller extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * @var \Designnbuy\Reseller\Model\Admin
     */
    protected $_reseller;

    public function __construct(
        ArrayManager $arrayManager,
        \Designnbuy\Reseller\Model\Admin $reseller,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->arrayManager = $arrayManager;
        $this->_reseller = $reseller;
        $this->authorization = $authorization;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
		/*foreach ($meta as $sectionName => &$section) {
			echo '<pre>';
			print_r($section);
		}exit;*/

		/* show/hide product tabs */
		//if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
				/*$meta = array_replace_recursive(
					$meta,
					[
						'search-engine-optimization' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'websites' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'review' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'related' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'schedule-design-update' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'gift-options' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'design' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
					$meta,
					[
						'downloadable' => [
							'arguments' => [
								'data' => [
									'config' => [
										'label' => __('')

									],
								],
							],
							'children' => [],
						],
					]
				);
				$meta = array_replace_recursive(
						$meta,
						[
							'custom_options' => [
								'arguments' => [
									'data' => [
										'config' => [
											'label' => __('')
										],
									],
								],
								'children' => [],
							],
						]
					);*/
		//}
		/*end show/hide product tabs */
        if($this->_reseller->isResellerAdmin()) {
            foreach ($meta as $sectionName => &$section) {
                /* show/hide create_configurable_products_button */
                $owner['arguments']['data']['config'] = [
                    'visible' => false,
                ];
                if ($sectionName == 'configurable' || $sectionName == 'websites' ) {
                    $this->arrayManager->merge(
                        "$sectionName",
                        $meta,
                        $owner
                    );
                }
                $this->arrayManager->merge(
                    "$sectionName/children/configurable_products_button_set/children/create_configurable_products_button",
                    $meta,
                    $owner
                );
                /*end show/hide create_configurable_products_button */
            }

            $disableFieldArray = array("attribute_set_id", "price", "advanced_pricing_button", "create_category_button", "advanced_inventory_button");
            /* start  show/hide product general tab data */
            //if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
            foreach ($meta as $sectionName => &$section) {
                foreach ($section as $index => $_container) {
                    foreach ($_container as $indexs => $arguments) {

                        $container = $indexs;
                        if ($container == 'container_price') {
                            $owner['arguments']['data']['config'] = [
                                'disabled' => true,
                            ];

                            $this->arrayManager->merge(
                                "$sectionName/children/" . $container,
                                $meta,
                                $owner
                            );

                        }
                        if ($container == 'attribute_set_id') {
                            $owner['arguments']['data']['config'] = [
                                'disabled' => true,
                            ];

                            $this->arrayManager->merge(
                                "$sectionName/children/" . $container,
                                $meta,
                                $owner
                            );
                        }
                        foreach ($arguments as $key => $data) {
                            foreach ($data as $key => $config) {
                                if ($key == 'data') {
                                    continue;
                                }
                                $attributr = $key;
                                foreach ($config as $row) {

                                    foreach ($row['data'] as $k) {

                                        if (in_array($attributr, $disableFieldArray)) {

                                            //$dataType = $k['dataType'];
                                            //$formElement = $k['formElement'];
                                            $owner['arguments']['data']['config'] = [
                                                'formElement' => $k['formElement'],
                                                //'dataType' => $k['dataType'],
                                                'readonly' => true,
                                                'disabled' => true,
                                            ];

                                            $this->arrayManager->merge(
                                                "$sectionName/children/" . $container . "/children/" . $attributr,
                                                $meta,
                                                $owner
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    goto outsideforeach;
                    break;
                }
            }

            //}
            /* End  show/hide product general tab data */
            outsideforeach: '';
            /* start  show/hide product tabs */
            unset($meta['review']);
            unset($meta['related']);
            unset($meta['custom_options']);
            //unset($meta['websites']);
            unset($meta['printingmethodtab']);
            unset($meta['designidea_category_tab']);
            unset($meta['fonttab']);
            unset($meta['customproduct-settings']);
            unset($meta['categorytab']);
            unset($meta['threed-configuration']);
            unset($meta['hotfolder-settings']);
            unset($meta['vendor']);
            unset($meta['weltpixel-options']);
            unset($meta['downloadable']);
            unset($meta['gift-options']);
            unset($meta['schedule-design-update']);
            unset($meta['design']);
            unset($meta['customcanvas-settings']);
            unset($meta['customcanvas-settings']);
            unset($meta['template_tab']);
            unset($meta['backgroundtab']);
            /* end show/hide product tabs */
        }

        return $meta;
    }
}
