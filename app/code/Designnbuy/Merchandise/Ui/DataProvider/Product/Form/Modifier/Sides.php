<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Merchandise\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Designnbuy\ConfigArea\Model\ConfigArea\Source\ConfigArea;
use Designnbuy\ConfigArea\Model\ConfigAreaFactory;
use Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory as SideConfigCollectionFactory;
use Designnbuy\Merchandise\Model\ConfigAreaFactory as SideConfigFactory;
/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sides extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{
    const CODE_NO_OF_SIDES = 'no_of_sides';
    const CODE_SIDES_CONFIGURATION = 'sides_configuration';
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
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Store manager
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigArea\Source\ConfigArea
     */
    protected $configArea;

    /**
     * Designidea factory
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigAreaFactory
     */
    protected $_configAreaFactory;

    /**
     * Designidea factory.
     *
     * @var \Designnbuy\Merchandise\Model\ConfigAreaFactory
     */
    protected $_sideConfigAreaFactory;
    /**
     * Designidea factory.
     *
     * @var \Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory
     */
    protected $_sideConfigAreaCollectionFactory;

    private $dataScopeName;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $dnbBaseHelper;
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
        ConfigArea $configArea,
        ConfigAreaFactory $_configAreaFactory,
        SideConfigFactory $_sideConfigAreaFactory,
        SideConfigCollectionFactory $_sideConfigAreaCollectionFactory,
        $dataScopeName,
        $scopeName = '',
        \Designnbuy\Base\Helper\Data $dnbBaseHelper
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->scopeName = $scopeName;
        $this->configArea = $configArea;
        $this->_configAreaFactory = $_configAreaFactory;
        $this->_sideConfigAreaFactory = $_sideConfigAreaFactory;
        $this->_sideConfigAreaCollectionFactory = $_sideConfigAreaCollectionFactory;
        $this->dataScopeName = $dataScopeName;
        $this->dnbBaseHelper = $dnbBaseHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;

        $model = $this->locator->getProduct();
        $customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();
        if($model && $model->getAttributeSetId() == $customProductAttributeSetId){

            $element_color_picker_type = $this->arrayManager->findPath('element_color_picker_type', $this->meta, null, 'children');
            //echo "<pre>"; print_r(get_class_methods($this->arrayManager)); exit;
            if($element_color_picker_type){
                $path = $this->arrayManager->slicePath($element_color_picker_type, 0).'/arguments/data/config/options/3';
                
                $this->meta = $this->arrayManager->remove(
                    $path,
                    $this->meta
                );
            }

            
            if(array_key_exists('downloadable',$this->meta)){
                unset($this->meta['downloadable']);
            }
        }


        if (isset($this->meta['sides-configuration'])) {
            $this->customizeSides();
            $this->addAdvancedSideLink();
            $this->customizeAdvancedSides();
            $this->customizeSideLayouts();
        }
        if($model && $model->getAttributeSetId() == $customProductAttributeSetId){
            if (isset($this->meta['design-studio-settings']['children']['container_bg_color_picker_type'])) {
                if (isset($this->meta['design-studio-settings']['children']['container_bg_color_picker_type']['children']['bg_color_picker_type']['arguments']['data']['config']['options'][3])) {
                    unset($this->meta['design-studio-settings']['children']['container_bg_color_picker_type']['children']['bg_color_picker_type']['arguments']['data']['config']['options'][3]);
                }
            }
        }
        unset($this->meta['design-studio-settings']['children']['container_allow_scratch']);
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
        $configAreas = [];

        $configAreaCollection = $this->_configAreaFactory->create()->getResourceCollection()->addActiveFilter();
        foreach ($configAreaCollection as $configArea) {
            $configAreas[] = [
                    'id' => $configArea->getConfigareaId(),
                    'title' => $configArea->getTitle(),
                    'image' => $configArea->getImage(),
                    'area' => json_decode($configArea->getArea()),
            ];

        }
        $data[$productId][self::DATA_SOURCE_DEFAULT]['configurable_areas'] = $configAreas;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['configurable_areas']['saveUrl'] = $this->urlBuilder->addSessionParam()->getUrl(
            'merchandise/side_configarea/save',
            ['product_id'=> $productId ,'_secure' => true]
        );

        return $data;
    }


    /**
     * Customize side field
     *
     * @return $this
     */
    protected function customizeSides()
    {
        $sideConfigurationPath = $this->arrayManager->findPath(
            self::CODE_SIDES_CONFIGURATION,
            $this->meta,
            null,
            'children'
        );

        if ($sideConfigurationPath) {

            $fields = $this->arrayManager->merge('children/record/children',$this->getSideConfigurationStructure($sideConfigurationPath),$this->_createStoreSpecificField());



            /*$this->meta = $this->arrayManager->merge(
                $sideConfigurationPath,
                $this->meta,
                $this->getSideConfigurationStructure($sideConfigurationPath)
            );*/
            $this->meta = $this->arrayManager->merge(
                $sideConfigurationPath,
                $this->meta,
                $fields
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($sideConfigurationPath, 0, -3)
                . '/' . self::CODE_SIDES_CONFIGURATION,
                $this->meta,
                $this->arrayManager->get($sideConfigurationPath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($sideConfigurationPath, 0, -2),
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
    protected function addAdvancedSideLink()
    {
        $sidePath = $this->arrayManager->findPath(
            self::CODE_NO_OF_SIDES,
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
                        'targetName' => $this->scopeName . '.advanced_pages_modal',
                        'actionName' => 'toggleModal',
                    ]
                ],
                'title' => __('Upload Images and Configure Design Area'),
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
     * @param string $sideConfigurationPath
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getSideConfigurationStructure($sideConfigurationPath)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'dynamicRows',
                        'label' => __('Side Configuration'),
                        'renderDefaultRecord' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => '',
                        'addButton' => false,
                        'dndConfig' => [
                            'enabled' => false,
                        ],
                        'additionalClasses' => 'admin__field-wide',
                        'disabled' => false,
                        'imports' => ['insertData' => '${ $.provider }'],
                        'sortOrder' =>
                            $this->arrayManager->get($sideConfigurationPath . '/arguments/data/config/sortOrder', $this->meta),
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
                        'label' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Input::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'label' => __('Side Label'),
                                        'dataScope' => 'label',
                                        'validation' => [
                                            'required-entry' => true
                                        ],
                                        'scopeLabel' => null,
                                        'additionalClasses' => 'admin__fieldbackend/en_US/mage/adminhtml/grid.js-small',
                                        'sortOrder' => 0,
                                    ],
                                ],
                            ],
                        ],
                        'value_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'formElement' => Hidden::NAME,
                                        'componentType' => Field::NAME,
                                        'dataType' => Text::NAME,
                                        'dataScope' => 'value_id',
                                    ],
                                ],
                            ],
                        ],
                        'side_image' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Container::NAME,
                                        'formElement' => Container::NAME,
                                        'component' => 'Magento_Ui/js/form/components/group',
                                        'label' => __('Side Image'),
                                        'dataScope' => '',
                                        'showLabel'         => false,
                                        //'sortOrder' => 100,
                                    ],
                                ],
                            ],
                            'children' => [
                                'image' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'fileUploader',
                                                'componentType' => 'fileUploader',
                                                'component' => 'Designnbuy_Merchandise/js/components/file-uploader',
                                                'elementTmpl' => 'Designnbuy_Merchandise/components/file-uploader',
                                                'fileInputName' => 'image',
                                                'uploaderConfig' => [
                                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                                        'merchandise/side_image/upload',
                                                        ['type' => 'image', '_secure' => true]
                                                    ),
                                                ],
                                                'dataScope' => 'image.file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'mask_image' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Container::NAME,
                                        'formElement' => Container::NAME,
                                        'component' => 'Magento_Ui/js/form/components/group',
                                        'label' => __('Mask Image'),
                                        'dataScope' => '',
                                        'showLabel'         => false,
                                        //'sortOrder' => 100,
                                    ],
                                ],
                            ],
                            'children' => [
                                'image' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'fileUploader',
                                                'componentType' => 'fileUploader',
                                                'component' => 'Designnbuy_Merchandise/js/components/file-uploader',
                                                'elementTmpl' => 'Designnbuy_Merchandise/components/file-uploader',
                                                'fileInputName' => 'mask',
                                                'uploaderConfig' => [
                                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                                        'merchandise/side_image/upload',
                                                        ['type' => 'mask', '_secure' => true]
                                                    ),
                                                ],
                                                'dataScope' => 'mask.file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'overlay_image' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Container::NAME,
                                        'formElement' => Container::NAME,
                                        'component' => 'Magento_Ui/js/form/components/group',
                                        'label' => __('Overlay Image'),
                                        'dataScope' => '',
                                        'showLabel'         => false,
                                        //'sortOrder' => 100,
                                    ],
                                ],
                            ],
                            'children' => [
                                'image' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'fileUploader',
                                                'componentType' => 'fileUploader',
                                                'component' => 'Designnbuy_Merchandise/js/components/file-uploader',
                                                'elementTmpl' => 'Designnbuy_Merchandise/components/file-uploader',
                                                'fileInputName' => 'overlay',
                                                'uploaderConfig' => [
                                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                                        'merchandise/side_image/upload',
                                                        ['type' => 'overlay', '_secure' => true]
                                                    ),
                                                ],
                                                'dataScope' => 'overlay.file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        'configure_areas' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'label' => __('Design Areas'),
                                        'formElement' => 'multiselect',
                                        'componentType' => 'field',
                                        'component' => 'Magento_Ui/js/form/element/multiselect',
                                        'options' => $this->getConfigureAreas(),
                                        'dataScope' => 'configure_areas',
                                        'sortOrder' => 10,
                                        'dataType' => Number::NAME,
                                        'scopeLabel' => null,
                                    ],
                                ],
                            ],
                        ],
                        'edit' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'displayAsLink' => false,
                                        'formElement' => Container::NAME,
                                        'componentType' => Container::NAME,
                                        'component' => 'Designnbuy_Merchandise/js/components/configarea',
                                        //'component' => 'Magento_Ui/js/form/components/button',
                                        'template' => 'ui/form/components/button/container',
                                        'actions' => [
                                            [
                                                'targetName' => $this->dataScopeName.'.configureAreaModal',
                                                //'actionName' => 'trigger',
                                                'actionName' => 'setConfigIds',
                                                'params' => [
                                                    [
                                                        'provider' => '${ $.provider }',
                                                        'parentScope' => '${ $.parentScope }',
                                                    ],
                                                ],
                                            ],
                                            [
                                                'targetName' => $this->scopeName . '.advanced_sides_layout_modal',
                                                'actionName' => 'toggleModal',

                                            ]
                                        ],
                                        'title' => __('Edit Design Areas'),
                                        'additionalForGroup' => true,
                                        'provider' => false,
                                        'source' => 'product_details',
                                        'sortOrder' => 100,
                                        'dataScope' => 'Configure',
                                    ],
                                ],
                            ],
                        ],
                        'color_image' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Container::NAME,
                                        'formElement' => Container::NAME,
                                        'component' => 'Magento_Ui/js/form/components/group',
                                        'label' => __('Color Thumbnail'),
                                        'showLabel'         => false,
                                        'dataScope' => '',
                                        //'sortOrder' => 100,
                                    ],
                                ],
                            ],
                            'children' => [
                                'image' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'fileUploader',
                                                'componentType' => 'fileUploader',
                                                'component' => 'Designnbuy_Merchandise/js/components/file-uploader',
                                                'elementTmpl' => 'Designnbuy_Merchandise/components/file-uploader',
                                                'fileInputName' => 'color',
                                                'uploaderConfig' => [
                                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                                        'merchandise/side_image/upload',
                                                        ['type' => 'color', '_secure' => true]
                                                    ),
                                                ],
                                                'dataScope' => 'color.file',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                        /*'edit_config' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'component' => 'Designnbuy_Merchandise/js/components/container-configarea-handler',
                                        'formElement' => 'container',
                                        'componentType' => 'container',
                                        'label' => false,
                                        'content1' => __(
                                            'Add some content'
                                        ),
                                        'template' => 'ui/form/components/complex',
                                        'createTabButton' => 'ns = ${ $.ns }, index = create_configarea_products_button',
                                        'visible' => true,
                                    ],
                                ],
                            ],
                            'children' => [
                                'create_configarea_products_button' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'formElement' => 'container',
                                                'componentType' => 'container',
                                                'template' => 'ui/form/components/button/container',
                                                'component' => 'Magento_Ui/js/form/components/button',
                                                'actions' => [
                                                    [
                                                        'targetName' => $this->dataScopeName.'.configureAreaModal',
                                                        'actionName' => 'trigger',
                                                        'params' => ['active', true],
                                                    ],
                                                    [
                                                        'targetName' => $this->dataScopeName.'.configureAreaModal',
                                                        'actionName' => 'setConfigIds',
                                                        'params' => [
                                                            [
                                                                'provider' => '${ $.provider }',
                                                                'parentScope' => '${ $.parentScope }',
                                                            ],
                                                        ],
                                                    ],
                                                ],
                                                'title' => __('Configure Area'),
                                                'sortOrder' => 20,
                                                'visible' => true,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ]*/
                        /*'actionDelete' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'actionDelete',
                                        'dataType' => Text::NAME,
                                        'label' => '',
                                    ],
                                ],
                            ],
                        ],*/
                    ],
                ],
            ],
        ];
    }

    protected function getConfigureAreas()
    {
        $configOptions = $this->configArea->getAllOptions();
        return $configOptions;
    }


    /**
     * Customize Upload Images and Configure Design Area Panel
     *
     * @return $this
     */
    protected function customizeSideLayouts()
    {
        $content = __(
            'Configuration area is where users can add printing elements. Standard sizes are available for selection. You can also adjust design area for this product individually by repositioning it.'
        );
        $this->meta['advanced_sides_layout_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'content' => $content,
            'options' => [
                'title' => __('Design Areas'),
                'buttons' => [
                    [
                        'text' => __('Continue Edit'),
                        'class' => 'action-primary',
                        'actions' => [
                            'actionDone',
                        ],
                    ],
                ],
            ],

        ];

        $this->meta['advanced_sides_layout_modal']['children']['configure-area-configuration'] = $this->getConfigureAreasModal(40);


        return $this;
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getConfigureAreasModal($sortOrder)
    {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Configure Area'),
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'sortOrder' => $sortOrder,
                        //'content' => $content,
                    ],
                ],
            ],
            'children' => [
                'config_area_container' => $this->getConfigureAreaContainer(10),
            ],
        ];
    }

    protected function getConfigureAreaContainer($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => '',
                        'formElement' => Fieldset::NAME,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' => $sortOrder,
                        'additionalClasses' => 'admin__field-wide',
                    ],
                ],
            ],
            'children' => [
                'config_area' => $this->getAreaConfig(10)
            ],
        ];
    }

    protected function getAreaConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/html',
                       // 'component' => 'Designnbuy_ConfigArea/js/components/tab',
                        'template' => 'Designnbuy_ConfigArea/components/configarea',
                        'sortOrder' => $sortOrder,
                        'dataScope' => 'name',
                        'fileInputName' => 'color',
                        //'flashvars' => $this->getFlashVars(),
                    ],
                ],
            ],
            'children' => [],
        ];
    }

    /*protected function getConfigureAreasModal()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => Input::NAME,
                        'componentType' => Field::NAME,
                        'component' => 'Magento_Ui/js/form/components/area',
                        'dataType' => Text::NAME,
                        'label' => '',
                        'dataScope' => '',
                        'validation' => [
                            // 'required-entry' => true
                        ],
                        'additionalClasses' => 'admin__field-small'
                        //'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];
    }*/
    /**
     * Customize Upload Images and Configure Design Area Panel
     *
     * @return $this
     */
    protected function customizeAdvancedSides()
    {
        $this->meta['sides-configuration']['arguments']['data']['config']['opened'] = true;
        $this->meta['sides-configuration']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['sides-configuration']['arguments']['data']['config']['label'] = '';

        $this->meta['advanced_pages_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'options' => [
                'title' => __('Upload Images and Configure Design Area'),
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
                static::CONTAINER_PREFIX . self::CODE_NO_OF_SIDES,
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

        $this->meta['advanced_pages_modal']['children']['sides-configuration'] = $this->meta['sides-configuration'];

        unset($this->meta['sides-configuration']);

        /*$this->meta['advanced_pages_modal']['children']['advanced-pages'] = $this->meta['advanced-pricing'];
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
}
