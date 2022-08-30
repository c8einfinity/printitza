<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Pricecalculator\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductAttributeInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Price;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Customer\Source\GroupSourceInterface;
/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SquareArea extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const CODE_CUSTOM_HEIGHT_WIDTH = 'enable_custom_height_width';
    const CODE_SQUARE_AREA_PRICE = 'square_area_price';
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var GroupManagementInterface
     * @since 101.0.0
     */
    protected $groupManagement;


    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;


    /**
     * @var Data
     */
    protected $directoryHelper;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var string
     */
    protected $scopeName;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;


    /**
     * @var GroupSourceInterface
     */
    private $customerGroupSource;
    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ModuleManager $moduleManager
     * @param Data $directoryHelper
     * @param ArrayManager $arrayManager
     * @param string $scopeName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GroupManagementInterface $groupManagement,
        ModuleManager $moduleManager,
        Data $directoryHelper,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        $scopeName = '',
         GroupSourceInterface $customerGroupSource = null
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;

        $this->groupManagement = $groupManagement;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->scopeName = $scopeName;
        $this->customerGroupSource = $customerGroupSource
            ?: ObjectManager::getInstance()->get(GroupSourceInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;


        if (isset($this->meta['advanced-custom-width-height-pricing'])) {
            $this->customizeSquareArea();
            $this->addAdvancedSquareAreaLink();
            $this->customizeAdvancedSquareArea();
        }

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        /*$linkData['file'][0] = [
            'file' => 'a/j/ajay_1.jpg',
            'name' => 'ajay_1.jpg',
            'size' => 879394,
            'status' => 'old',
            'url' => 'http://192.168.0.75/dnb_products/AIODV30/pub/media/designnbuy/side/a/j/ajay_1.jpg'
        ];

        $items = [
            [
                'label' => 'Front',
                'image' => $linkData
            ],
            [
                'label' => 'Back',
                'image' => ''
            ],
            [
                'label' => 'Left',
                'image' => ''
            ],
            [
                'label' => 'Right',
                'image' => ''
            ],
        ];*/
        //$data[$productId][self::DATA_SOURCE_DEFAULT][self::CODE_SQUARE_AREA_PRICE] = $items;

        return $data;
    }


    /**
     * Customize side field
     *
     * @return $this
     */
    protected function customizeSquareArea()
    {
        $squareAreaConfigurationPath = $this->arrayManager->findPath(
            self::CODE_SQUARE_AREA_PRICE,
            $this->meta,
            null,
            'children'
        );

        if ($squareAreaConfigurationPath) {

           // $fields = $this->arrayManager->merge('children/record/children',$this->getSquareAreaConfigurationStructure($squareAreaConfigurationPath),$this->_createStoreSpecificField());
            $fields = $this->arrayManager->merge('children/record/children',$this->getSquareAreaConfigurationStructure($squareAreaConfigurationPath),[]);

            /*$this->meta = $this->arrayManager->merge(
                $squareAreaConfigurationPath,
                $this->meta,
                $this->getSquareAreaConfigurationStructure($squareAreaConfigurationPath)
            );*/
            $this->meta = $this->arrayManager->merge(
                $squareAreaConfigurationPath,
                $this->meta,
                $fields
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($squareAreaConfigurationPath, 0, -3)
                . '/' . self::CODE_SQUARE_AREA_PRICE,
                $this->meta,
                $this->arrayManager->get($squareAreaConfigurationPath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($squareAreaConfigurationPath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }


    /**
     * Add link to open Advanced Pricing Panel
     *
     * @return $this
     */
    protected function addAdvancedSquareAreaLink()
    {
        $sidePath = $this->arrayManager->findPath(
            self::CODE_CUSTOM_HEIGHT_WIDTH,
            $this->meta,
            null,
            'children'
        );

        if ($sidePath) {
            $this->meta = $this->arrayManager->merge(
                $sidePath . '/arguments/data/config',
                $this->meta,
                ['additionalClasses' => 'admin__field-small']
            );

            $advancedPricingButton['arguments']['data']['config'] = [
                'displayAsLink' => true,
                'formElement' => Container::NAME,
                'componentType' => Container::NAME,
                'component' => 'Magento_Ui/js/form/components/button',
                'template' => 'ui/form/components/button/container',
                'actions' => [
                    [
                        'targetName' => $this->scopeName . '.advanced_custom_width_height_pricing_modal',
                        'actionName' => 'toggleModal',
                    ]
                ],
                'title' => __('Square Area Configuration'),
                'additionalForGroup' => true,
                'provider' => false,
                'source' => 'product_details',
                'sortOrder' =>
                    $this->arrayManager->get($sidePath . '/arguments/data/config/sortOrder', $this->meta) + 1,
            ];

            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($sidePath, 0, -1) . '/advanced_pages_button',
                $this->meta,
                $advancedPricingButton
            );
        }

        return $this;
    }

    /**
     * Get side dynamic rows structure
     *
     * @param string $squareAreaConfigurationPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getSquareAreaConfigurationStructure($squareAreaConfigurationPath)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Square Area Configuration'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'addButton' => true,
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'additionalClasses' => 'admin__field-wide',
                        'disabled' => false,
                        'sortOrder' =>
                            $this->arrayManager->get($squareAreaConfigurationPath . '/arguments/data/config/sortOrder', $this->meta),
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => [
                        'website_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Text::NAME,
                                        'formElement' => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataScope' => 'website_id',
                                        'label' => __('Website'),
                                        'options' => $this->getWebsites(),
                                        'value' => $this->getDefaultWebsite(),
                                        'visible' => $this->isMultiWebsites(),
                                        'disabled' => ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()),
                                        'sortOrder' => 10,
                                    ],
                                ],
                            ],
                        ],
                        'cust_group' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Select::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'dataScope' => 'cust_group',
                                        'label' => __('Customer Group'),
                                        'options' => $this->getCustomerGroups(),
                                        'value' => $this->getDefaultCustomerGroup(),
                                        'sortOrder' => 20,
                                    ],
                                ],
                            ],
                        ],
                        'price_qty' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Square Area'),
                                        'dataScope' => 'price_qty',
                                        'sortOrder' => 30,
                                        'validation' => [
                                            'required-entry' => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-greater-than-zero' => true,
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'price' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Field::NAME,
                                        'formElement' => Input::NAME,
                                        'dataType' => Price::NAME,
                                        'label' => __('Price'),
                                        'enableLabel' => true,
                                        'dataScope' => 'price',
                                        'addbefore' => $this->locator->getStore()
                                            ->getBaseCurrency()
                                            ->getCurrencySymbol(),
                                        'sortOrder' => 40,
                                        'validation' => [
                                            'required-entry' => true,
                                            'validate-greater-than-zero' => true,
                                            'validate-number' => true,
                                        ],
                                        'imports' => [
                                            'priceValue' => '${ $.provider }:data.product.price',
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                        'sortOrder' => 50,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    /**
     * Customize Advanced Sides Panel
     *
     * @return $this
     */
    protected function customizeAdvancedSquareArea()
    {
        $this->meta['advanced-custom-width-height-pricing']['arguments']['data']['config']['opened'] = true;
        $this->meta['advanced-custom-width-height-pricing']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['advanced-custom-width-height-pricing']['arguments']['data']['config']['label'] = '';

        $this->meta['advanced_custom_width_height_pricing_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'options' => [
                'title' => __('Advanced Square Area Price'),
                'buttons' => [
                    [
                        'text' => __('Done'),
                        'class' => 'action-primary',
                        'actions' => [
                            [
                                'targetName' => '${ $.name }',
                                'actionName' => 'actionDone'
                            ]
                        ]
                    ],
                ],
            ],
        ];

        $this->meta = $this->arrayManager->merge(
            $this->arrayManager->findPath(
                static::CONTAINER_PREFIX . self::CODE_CUSTOM_HEIGHT_WIDTH,
                $this->meta,
                null,
                'children'
            ),
            $this->meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'component' => 'Magento_Ui/js/form/components/group',
                        ],
                    ],
                ],
            ]
        );

        $this->meta['advanced_custom_width_height_pricing_modal']['children']['advanced-custom-width-height-pricing'] = $this->meta['advanced-custom-width-height-pricing'];
        unset($this->meta['advanced-custom-width-height-pricing']);
        /*$this->meta['advanced_custom_width_height_pricing_modal']['children']['advanced-pages'] = $this->meta['advanced-pricing'];
        unset($this->meta['advanced-pricing']);*/

        return $this;
    }

    /**
     * Retrieve store
     *
     * @return \Magento\Store\Model\Store
     */
    protected function getStore()
    {
        return $this->locator->getStore();
    }

    protected function _createStoreSpecificField()
    {
        $storeFields = [];
        $sortOrder = 0;
        foreach ($this->storeManager->getWebsites() as $website) {
            foreach ($website->getGroups() as $group) {
                $stores = $group->getStores();
                if (count($stores) == 0) {
                    continue;
                }
                /*$storeMeta['stores'] = [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'formElement' => Container::NAME,
                                'component' => 'Magento_Ui/js/form/components/group',
                                'label' => __('Stores'),
                                'dataScope' => 'stores',

                                //'sortOrder' => 100,
                            ],
                        ],
                    ],
                    'children' => [ ],
                ];*/
                foreach ($stores as $store) {
                    $storeFields[$store->getCode()] = [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'formElement' => Input::NAME,
                                    'componentType' => Field::NAME,
                                    'dataType' => Text::NAME,
                                    'label' => $store->getName(),
                                    'dataScope' => $store->getCode(),
                                    'validation' => [
                                        // 'required-entry' => true
                                    ],
                                    'additionalClasses' => 'admin__field-small'
                                    //'sortOrder' => $sortOrder,
                                ],
                            ],
                        ],
                    ];
                    $sortOrder++;
                }
            }
        }
        //$storeMeta['stores']['children'] = $storeFields;
        //return $storeMeta;
        return $storeFields;
    }

    /**
     * Retrieve allowed customer groups
     *
     * @return array
     */
    private function getCustomerGroups()
    {
        if (!$this->moduleManager->isEnabled('Magento_Customer')) {
            return [];
        }

        return $this->customerGroupSource->toOptionArray();
    }

    /**
     * Check tier_price attribute scope is global
     *
     * @return bool
     */
    private function isScopeGlobal()
    {
        return $this->locator->getProduct()
            ->getResource()
            ->getAttribute(self::CODE_SQUARE_AREA_PRICE)
            ->isScopeGlobal();
    }

    /**
     * Get websites list
     *
     * @return array
     */
    private function getWebsites()
    {
        $websites = [
            [
                'label' => __('All Websites') . ' [' . $this->directoryHelper->getBaseCurrencyCode() . ']',
                'value' => 0,
            ]
        ];
        $product = $this->locator->getProduct();

        if (!$this->isScopeGlobal() && $product->getStoreId()) {
            /** @var \Magento\Store\Model\Website $website */
            $website = $this->getStore()->getWebsite();

            $websites[] = [
                'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                'value' => $website->getId(),
            ];
        } elseif (!$this->isScopeGlobal()) {
            $websitesList = $this->storeManager->getWebsites();
            $productWebsiteIds = $product->getWebsiteIds();
            foreach ($websitesList as $website) {
                /** @var \Magento\Store\Model\Website $website */
                if (!in_array($website->getId(), $productWebsiteIds)) {
                    continue;
                }
                $websites[] = [
                    'label' => $website->getName() . '[' . $website->getBaseCurrencyCode() . ']',
                    'value' => $website->getId(),
                ];
            }
        }

        return $websites;
    }

    /**
     * Retrieve default value for customer group
     *
     * @return int
     */
    private function getDefaultCustomerGroup()
    {
        return $this->groupManagement->getAllCustomersGroup()->getId();
    }

    /**
     * Retrieve default value for website
     *
     * @return int
     * @since 101.0.0
     */
    public function getDefaultWebsite()
    {
        if ($this->isShowWebsiteColumn() && !$this->isAllowChangeWebsite()) {
            return $this->storeManager->getStore($this->locator->getProduct()->getStoreId())->getWebsiteId();
        }

        return 0;
    }

    /**
     * Show group prices grid website column
     *
     * @return bool
     */
    private function isShowWebsiteColumn()
    {
        if ($this->isScopeGlobal() || $this->storeManager->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * Show website column and switcher for group price table
     *
     * @return bool
     */
    private function isMultiWebsites()
    {
        return !$this->storeManager->isSingleStoreMode();
    }

    /**
     * Check is allow change website value for combination
     *
     * @return bool
     */
    private function isAllowChangeWebsite()
    {
        if (!$this->isShowWebsiteColumn() || $this->locator->getProduct()->getStoreId()) {
            return false;
        }
        return true;
    }
}
