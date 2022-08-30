<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Stdlib\ArrayManager;

class Owner extends AbstractModifier
{
    /**
     * @var ArrayManager
     */
    protected $arrayManager;
    /**
     * @var \Amasty\Rolepermissions\Model\Entity\Attribute\Source\Admins
     */
    protected $admins;
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    public function __construct(
        ArrayManager $arrayManager,
        \Amasty\Rolepermissions\Model\Entity\Attribute\Source\Admins $admins,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->arrayManager = $arrayManager;
        $this->admins = $admins;
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
		if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
				$meta = array_replace_recursive(
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
				/*$meta = array_replace_recursive(
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
				);*/
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
					);
		}
		/*end show/hide product tabs */
		$skiparray = array("advanced_pricing_button","product_has_weight","create_category_button","advanced_inventory_button");

        foreach ($meta as $sectionName => &$section) {
            if (isset($section['children']['container_amrolepermissions_owner'])) {

                if (!$this->authorization->isAllowed('Amasty_Rolepermissions::product_owner')) {
                    $this->arrayManager->remove(
                        "$sectionName/children/container_amrolepermissions_owner",
                        $meta
                    );
                }
                else {
                    $owner['arguments']['data']['config'] = [
                        'formElement' => 'select',
                        'dataType' => 'select',
                        'options' => $this->admins->getAllOptions()
                    ];

                    $this->arrayManager->merge(
                        "$sectionName/children/container_amrolepermissions_owner/children/amrolepermissions_owner",
                        $meta,
                        $owner
                    );
                }
            }

			/* show/hide create_configurable_products_button */

				if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
					$owner['arguments']['data']['config'] = [
						'disabled' => true,
					];
					$this->arrayManager->merge(
						"$sectionName/children/configurable_products_button_set/children/create_configurable_products_button",
						$meta,
						$owner
					);
				}
			/*end show/hide create_configurable_products_button */


        }


		/* start  show/hide product general tab data */
		if (!$this->authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
			foreach ($meta as $sectionName => &$section) {
				foreach ($section as $index => $_container) {
					foreach ($_container as $indexs => $arguments) {
						$container = $indexs;
						if($container == 'attribute_set_id'){continue;}
						foreach($arguments as $key => $data) {
							foreach($data as $key => $config) {
								if($key == 'data'){continue;}
								$attributr = $key;
								foreach($config as $row) {
									foreach($row['data'] as $k) {
									if (in_array($attributr, $skiparray))
										{
											continue;
										}
										$dataType = $k['dataType'];
										$formElement = $k['formElement'];
										$owner['arguments']['data']['config'] = [
											'formElement' => $k['formElement'],
											'dataType' => $k['dataType'],
											'readonly' => true,
											'disabled' => true,
										];

										$this->arrayManager->merge(
											"$sectionName/children/".$container."/children/".$attributr,
											$meta,
											$owner
										);
									}
								}
							}
						}
					}
					goto outsideforeach;
					break;
				}
			}
		}
			/* End  show/hide product general tab data */
			outsideforeach: '';

        return $meta;
    }
}
