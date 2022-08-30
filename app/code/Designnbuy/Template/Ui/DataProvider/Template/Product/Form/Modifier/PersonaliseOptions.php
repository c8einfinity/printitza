<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Template\Ui\DataProvider\Template\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PersonaliseOptions extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const CODE_PERSONALISE_OPTION = 'canvas_personalize_option';
    const CODE_SIDES_CONFIGURATION = 'sides_configuration';
    const CODE_DESIGNS = 'templates';
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


        if (isset($this->meta['canvas-personalisation'])) {
            $this->customizeDesignCategoryField();
            $this->customizeDesignField();
            $product = $this->locator->getProduct();
            $productId = $product->getId();
            $canvasPersonalizeOption = $product->getCanvasPersonalizeOption();
            if ($productId && $canvasPersonalizeOption != 4) {
            //if ($productId) {
                $this->addAdvancedDesignLink();
            }

            $this->customizeAdvancedSides();
            //$this->customizeSideLayouts();
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

        $data[$productId][self::DATA_SOURCE_DEFAULT][self::CODE_DESIGNS] = $this->getCategoryTemplates();
        return $data;
    }

    protected function getCategoryTemplates()
    {
        $categoryDesigns = [];
        $categories = $this->categoryCollectionFactory->create();
        foreach ($categories as $category){
            $templates = $category->getTemplateCollection();
            $designs = [];
            foreach ($templates as $template){
                $designs[] = [
                    'id' => $template->getId(),
                    'title' => $template->getTitle(),
                    'image' => $template->getImage()
                ];
            }
            $categoryDesigns[$category->getId()] = $designs;
        }
        return $categoryDesigns;
    }

    protected function getTemplates()
    {

    }

    /**
     * Customize Categories field
     *
     * @param array $meta
     * @return array
     */
    protected function customizeDesignCategoryField()
    {
        $fieldCode = 'template_category';
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
                            'label' => __('Template Category'),
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
                                    'formElement' => 'select',
                                    'componentType' => 'field',
                                    //'component' => 'Magento_Ui/js/form/element/ui-select',
                                    //'component' => 'Designnbuy_Template/js/components/template-category',
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
        $fieldCode = 'template_id';
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
                            'label' => __('Template'),
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
                                    'component' => 'Designnbuy_Template/js/components/design',
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
                                    'component' => 'Designnbuy_Template/js/templateimage/image',
                                    'disableLabel' => true,
                                    'filterOptions' => true,
                                    'showCheckbox' => false,
                                    'multiple' => false,
                                    'chipsEnabled' => true,
                                    'label' => __('Template'),
                                    'additionalClasses' => 'admin__field-large',
                                    'elementTmpl' => 'Designnbuy_Template/templateimage/image',
                                    'dataScope' => $fieldCode,
                                    'filterBy' => [
                                        'target' => '${ $.provider }:${ $.parentScope }.template_category',
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
                        'targetName' => $this->scopeName . '.advanced_template_modal',
                        'actionName' => 'toggleModal',
                    ]
                ],
                'title' => __('Set Default Design Template'),
                'additionalForGroup' => true,
                'provider' => false,
                'source' => 'product_details',
                'imports' => [
                    'visible' => '${ $.provider }:data.product.canvas_personalize_option:value=4',
                ],
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
     * Customize Advanced Sides Panel
     *
     * @return $this
     */
    protected function customizeAdvancedSides()
    {
        $this->meta['canvas-personalisation']['arguments']['data']['config']['opened'] = true;
        $this->meta['canvas-personalisation']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['canvas-personalisation']['arguments']['data']['config']['label'] = '';

        $this->meta['advanced_template_modal']['arguments']['data']['config'] = [
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

        $this->meta['advanced_template_modal']['children']['canvas-personalisation'] = $this->meta['canvas-personalisation'];
        unset($this->meta['canvas-personalisation']);
        /*$this->meta['advanced_pages_modal']['children']['advanced-pages'] = $this->meta['advanced-pricing'];
        unset($this->meta['advanced-pricing']);*/

        return $this;
    }
}
