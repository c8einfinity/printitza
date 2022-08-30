<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Designidea\Ui\DataProvider\Designidea\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\CheckboxSet;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonaliseOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const CODE_PERSONALISE_OPTION = 'merchandise_personalize_option';
    const CODE_SIDES_CONFIGURATION = 'sides_configuration';
    const CODE_DESIGNS = 'designideas';
    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ModuleManager
     */
    protected $moduleManager;


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
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
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
        ModuleManager $moduleManager,
        Data $directoryHelper,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder,
        CategoryCollectionFactory $categoryCollectionFactory,
        $scopeName = ''
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->scopeName = $scopeName;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;


        if (isset($this->meta['merchandise-personalisation'])) {
            $this->customizeDesignCategoryField();
            $this->customizeDesignField();
            $this->addAdvancedDesignLink();
            $this->customizePersonalisationOptions();
        }

        return $this->meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT][self::CODE_DESIGNS] = $this->getCategoryDesignIdeas();
        return $data;
    }

    protected function getCategoryDesignIdeas()
    {
        $categoryDesigns = [];
        $categories = $this->categoryCollectionFactory->create();
        foreach ($categories as $category){
            $designIdeas = $category->getDesignIdeaCollection();
            $designs = [];
            foreach ($designIdeas as $designIdea){
                $designs[] = [
                    'id' => $designIdea->getId(),
                    'title' => $designIdea->getTitle(),
                    'image' => $designIdea->getImage()
                ];
            }
            $categoryDesigns[$category->getId()] = $designs;
        }
        return $categoryDesigns;
    }

    /**
     * Customize Categories field
     *
     * @param array $meta
     * @return array
     */
    protected function customizeDesignCategoryField()
    {
        $fieldCode = 'designidea_category';
        $elementPath = $this->arrayManager->findPath($fieldCode, $this->meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $this->meta, null, 'children');

        if (!$elementPath) {
            return $this->meta;
        }

        $this->meta = $this->arrayManager->merge(
            $containerPath,
            $this->meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Editable Artworks Category'),
                            'dataScope' => '',
                            'breakLine' => false,
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'scopeLabel' => __('[GLOBAL]'),
                        ],
                    ],
                ],
                'children' => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    /*'formElement' => 'select',
                                    'componentType' => 'field',
                                    'component' => 'Designnbuy_Designidea/js/components/design',
                                    'disableLabel' => true,
                                    'filterOptions' => true,
                                    'showCheckbox' => false,
                                    'multiple' => false,
                                    'chipsEnabled' => true,
                                    'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                   // 'options' => $this->getCategories(),

                                    'config' => [
                                        'dataScope' => $fieldCode,
                                        'sortOrder' => 10,
                                    ],*/
                                    /*'productProvider' => 'product_form.product_form',
                                    'optionsProvider' => 'source.data.product.options',
                                    'component' => 'Designnbuy_Designidea/js/components/design',
                                    'dataType' => 'text',
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    'dataScope' => $fieldCode,
                                    'label' => __('DesignIdea Category'),
                                    'additionalClasses' => 'admin__field-large',*/
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    //'component' => 'Magento_Ui/js/form/element/ui-select',
                                    //'component' => 'Designnbuy_Designidea/js/components/designidea-category',
                                    'disableLabel' => true,
                                    'filterOptions' => true,
                                    'showCheckbox' => false,
                                    'multiple' => false,
                                    'chipsEnabled' => true,
                                    'additionalClasses' => 'admin__field-large',
                                    //'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                    'dataScope' => $fieldCode,
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );

        return $this->meta;
    }

    /**
     * Customize Categories field
     *
     * @param array $meta
     * @return array
     */
    protected function customizeDesignField()
    {
        $fieldCode = 'designidea_id';
        $elementPath = $this->arrayManager->findPath($fieldCode, $this->meta, null, 'children');
        $containerPath = $this->arrayManager->findPath(static::CONTAINER_PREFIX . $fieldCode, $this->meta, null, 'children');

        if (!$elementPath) {
            return $this->meta;
        }

        $this->meta = $this->arrayManager->merge(
            $containerPath,
            $this->meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('DesignIdea'),
                            'dataScope' => '',
                            'breakLine' => false,
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'scopeLabel' => __('[GLOBAL]'),
                        ],
                    ],
                ],
                'children' => [
                    $fieldCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    /*'formElement' => 'select',
                                    'componentType' => 'field',
                                    'component' => 'Designnbuy_Designidea/js/components/design',
                                    'disableLabel' => true,
                                    'filterOptions' => true,
                                    'showCheckbox' => false,
                                    'multiple' => false,
                                    'chipsEnabled' => true,
                                    'additionalClasses' => 'admin__field-large',
                                    'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                    //'options' => $this->getCategoriesTree(),
                                    'dataScope' => 'option_id',
                                    'config' => [
                                        'dataScope' => $fieldCode,
                                        'sortOrder' => 10,
                                    ],*/
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    'component' => 'Designnbuy_Designidea/js/designideaimage/image',
                                    'disableLabel' => true,
                                    'filterOptions' => true,
                                    'showCheckbox' => false,
                                    'multiple' => false,
                                    'chipsEnabled' => true,
                                    'label' => __('DesignIdea'),
                                    'additionalClasses' => 'admin__field-large',
                                    'elementTmpl' => 'Designnbuy_Designidea/designideaimage/image',
                                    'dataScope' => $fieldCode,
                                    'filterBy' => [
                                        'target' => '${ $.provider }:${ $.parentScope }.designidea_category',
                                        'field' => 'category_id'
                                    ]
                                ],
                            ],
                        ],
                    ]
                ]
            ]
        );

        return $this->meta;
    }

    /**
     * Add link to open Advanced Pricing Panel
     *
     * @return $this
     */
    protected function addAdvancedDesignLink()
    {
        $sidePath = $this->arrayManager->findPath(
            self::CODE_PERSONALISE_OPTION,
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
                        'targetName' => $this->scopeName . '.advanced_design_modal',
                        'actionName' => 'toggleModal',
                    ]
                ],
                'title' => __('Set Default Design Template'),
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
     * Customize Select Design Panel
     *
     * @return $this
     */
    protected function customizePersonalisationOptions()
    {
        $this->meta['merchandise-personalisation']['arguments']['data']['config']['opened'] = true;
        $this->meta['merchandise-personalisation']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['merchandise-personalisation']['arguments']['data']['config']['label'] = '';

        $this->meta['advanced_design_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'options' => [
                'title' => __('Set Default Design Template'),
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
                static::CONTAINER_PREFIX . self::CODE_PERSONALISE_OPTION,
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

        $this->meta['advanced_design_modal']['children']['merchandise-personalisation'] = $this->meta['merchandise-personalisation'];
        //$this->meta['advanced_design_modal']['children']['merchandise-personalisation']['children']['designs'] =  $this->getSideConfigurationStructure();

        unset($this->meta['merchandise-personalisation']);

        return $this;
    }

    /**
     * Get side dynamic rows structure
     *
     * @param string $sideConfigurationPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getSideConfigurationStructure()
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Editable Artworks'),
                        'columnsHeader' => true,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'addButton' => false,
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'additionalClasses' => 'admin__field-wide',
                        //'disabled' => false,
                        'pageSize' => 1000000,
                        'sortOrder' =>
                            10,
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
                        'value' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        //'elementTmpl' => 'Designnbuy_Base/form/element/radio',
                                        'component' => 'Designnbuy_Base/js/components/single-checkbox',
                                        //'radioInputName' => 'designs',
                                        'formElement' => Checkbox::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Number::NAME,
                                        'label' => __('Id'),
                                        'dataScope' => 'value',
                                        'scopeLabel' => null,
                                        //'multiple' => false,
                                        //'prefer' => 'radio',
                                        'prefer' => 'toggle',
                                        'additionalClasses' => 'admin__field-small',
                                        'sortOrder' => 0,
                                        'valueMap' => [
                                            'true' => 1,
                                            'false' => 0,
                                        ],
                                        'fit' => true,
                                        'hasUnique' => true,
                                    ],
                                ],
                            ],
                        ],
                        'label' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'component' => 'Magento_Ui/js/form/element/text',
                                        'dataType' => Text::NAME,
                                        'label' => __('Design Name'),
                                        'dataScope' => 'label',
                                        'scopeLabel' => null,
                                        'additionalClasses' => 'admin__field-small',
                                        'sortOrder' => 1,
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

}
