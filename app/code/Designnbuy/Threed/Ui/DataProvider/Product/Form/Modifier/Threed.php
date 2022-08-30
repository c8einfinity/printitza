<?php

namespace Designnbuy\Threed\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\App\Filesystem\DirectoryList;

class Threed extends AbstractModifier
{
    const SORT_ORDER = 1000;
    const CODE_MAP_IMAGE = 'map_image';
    const CODE_MODEL_3D = 'model_3d';
    const CODE_THREED_CONFIGURE_AREA = 'threed_configure_area';

    protected $locator;

    protected $websiteRepository;

    protected $groupRepository;

    protected $storeRepository;

    protected $websitesOptionsList;

    protected $storeManager;

    protected $websitesList;

    private $dataScopeName;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Designnbuy\Threed\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        UrlInterface $urlBuilder,
        ArrayManager $arrayManager,
        \Designnbuy\Threed\Helper\Data $helper,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        $dataScopeName
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->storeRepository = $storeRepository;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->_helper = $helper;
        $this->mediaConfig = $mediaConfig;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->dataScopeName = $dataScopeName;
    }

    public function modifyData(array $data)
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        $data[$productId][self::DATA_SOURCE_DEFAULT][self::CODE_MAP_IMAGE] = $this->addImageFile(self::CODE_MAP_IMAGE,$product->getData(self::CODE_MAP_IMAGE));
        $data[$productId][self::DATA_SOURCE_DEFAULT][self::CODE_MODEL_3D] = $this->addImageFile(self::CODE_MODEL_3D,$product->getData(self::CODE_MODEL_3D));
        $data[$productId][self::DATA_SOURCE_DEFAULT]['threed_configurable_areas']['saveUrl'] = $this->urlBuilder->addSessionParam()->getUrl(
            'threed/configarea/save',
            ['product_id'=> $productId ,'_secure' => true]
        );
        $data[$productId][self::DATA_SOURCE_DEFAULT]['threed_configure_area'] = $product->getThreedConfigureArea();
        return $data;
    }
    /**
     * Add Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function addImageFile($attributeCode, $data)
    {
        $imageData = [];
        if (isset($data)) {
            $image = $data;
            $file = $this->mediaConfig->getMediaPath($image);
            if ($this->mediaDirectory->isExist($this->mediaConfig->getMediaPath($image))) {
                $imageData['file'][0] = [
                    'file' => $image,
                    'name' => $this->_helper->getFileFromPathFile($image),
                    //'size' => $this->mediaDirectory->stat($file)['size'],
                    'status' => 'old',
                    'url' => $this->mediaConfig->getMediaUrl($image),
                ];
                return $imageData;
            }
        }
        return '';
    }
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        
        if (isset($this->meta['3d-preview-settings'])) {
            $this->customizeMapImage();
            $this->customize3DModel();
            $this->customizeThreedConfigureAreaField();
            $this->customizeAdvanced3DPreviewSettings();

            $this->meta = array_replace_recursive(
                $this->meta,
                [
                    'threed-configuration' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalClasses' => 'admin__fieldset-product-customclass',
                                    'label' => __('3D Preview Configuration'),
                                    'collapsible' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'sortOrder' => $this->getNextGroupSortOrder(
                                        $this->meta,
                                        'vendor-settings',
                                        self::SORT_ORDER
                                    ),
                                    'disabled' => false,
                                    'visible' => true,
                                ],
                            ],
                        ],
                        'children' => $this->getPanelChildren(),
                    ],
                ]
            );
        }
        
        return $this->meta;
    }

    /**
     * Customize map_image field
     *
     * @return $this
     */
    protected function customizeMapImage()
    {
        $mapImagePath = $this->arrayManager->findPath(
            self::CODE_MAP_IMAGE,
            $this->meta,
            null,
            'children'
        );

        if ($mapImagePath) {
            $this->meta = $this->arrayManager->merge(
                $mapImagePath,
                $this->meta,
                $this->get3DMapImageContainer($this->arrayManager->get($mapImagePath . '/arguments/data/config/sortOrder', $this->meta))
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($mapImagePath, 0, -3)
                . '/' . self::CODE_MAP_IMAGE,
                $this->meta,
                $this->arrayManager->get($mapImagePath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($mapImagePath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }

    /**
     * Customize model_3d field
     *
     * @return $this
     */
    protected function customize3DModel()
    {
        $mapImagePath = $this->arrayManager->findPath(
            self::CODE_MODEL_3D,
            $this->meta,
            null,
            'children'
        );

        if ($mapImagePath) {
            $this->meta = $this->arrayManager->merge(
                $mapImagePath,
                $this->meta,
                $this->get3DModelContainer($this->arrayManager->get($mapImagePath . '/arguments/data/config/sortOrder', $this->meta))
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($mapImagePath, 0, -3)
                . '/' . self::CODE_MODEL_3D,
                $this->meta,
                $this->arrayManager->get($mapImagePath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($mapImagePath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }

    /**
     * Customize map_image field
     *
     * @return $this
     */
    protected function customizeThreedConfigureAreaField()
    {        
        $threedConfigureAreaPath = $this->arrayManager->findPath(
            self::CODE_THREED_CONFIGURE_AREA,
            $this->meta,
            null,
            'children'
        );

        if ($threedConfigureAreaPath) {
            $this->meta = $this->arrayManager->merge(
                $threedConfigureAreaPath,
                $this->meta,
                $this->getThreedConfigureAreaField($this->arrayManager->get($threedConfigureAreaPath . '/arguments/data/config/sortOrder', $this->meta))
            );
            $this->meta = $this->arrayManager->set(
                $this->arrayManager->slicePath($threedConfigureAreaPath, 0, -3)
                . '/' . self::CODE_THREED_CONFIGURE_AREA,
                $this->meta,
                $this->arrayManager->get($threedConfigureAreaPath, $this->meta)
            );
            $this->meta = $this->arrayManager->remove(
                $this->arrayManager->slicePath($threedConfigureAreaPath, 0, -2),
                $this->meta
            );
        }

        return $this;
    }

    public function getThreedConfigureAreaField($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('3D Configure Area'),
                        'sortOrder' => $sortOrder,
                        'collapsible' => true,
                        'formElement' => 'container',
                        'componentType' => 'container',
                    ]
                ]
            ],
            'children' => [
                self::CODE_THREED_CONFIGURE_AREA => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => Hidden::NAME,
                                'componentType' => Field::NAME,
                                'dataType' => Text::NAME,
                                'visible' => 1,
                                'required' => 1,
                                'label' => __('3D Configure Area')
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Customize Advanced Pricing Panel
     *
     * @return $this
     */
    protected function customizeAdvanced3DPreviewSettings()
    {
        $this->meta['3d-preview-settings']['arguments']['data']['config']['opened'] = true;
        $this->meta['3d-preview-settings']['arguments']['data']['config']['collapsible'] = false;
        $this->meta['3d-preview-settings']['arguments']['data']['config']['label'] = '';
        $this->meta['3d-preview-settings']['children'][] = $this->getThreedConfigureAreaConfig(40);

        $this->meta['advanced_threed_modal']['arguments']['data']['config'] = [
            'isTemplate' => false,
            'componentType' => Modal::NAME,
            'dataScope' => '',
            'provider' => 'product_form.product_form_data_source',
            'onCancel' => 'actionDone',
            'options' => [
                'title' => __('3D Preview Configuration'),
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

        $this->meta['advanced_threed_modal']['children']['3d-preview-settings'] = $this->meta['3d-preview-settings'];
        unset($this->meta['3d-preview-settings']);

        return $this;
    }

    /**
     * Get config for header container
     *
     * @param int $sortOrder
     * @return array
     */
    protected function getThreedConfigureAreaConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Configure Area'),
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'sortOrder' => $sortOrder,
                        //'content' => __('Sample content.'),
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
                        'label' => __('Configure Area'),
                        'formElement' => Fieldset::NAME,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' => $sortOrder,
                        'additionalClasses' => 'admin__field-wide',
                    ],
                ],
            ],
            'children' => [
                'threed_config_area' => $this->getAreaConfig(10)
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
                        //'component' => 'Magento_Ui/js/form/components/html',
                        "component" =>  "Designnbuy_Threed/js/components/threed-config-area",
                        'template' => 'Designnbuy_Threed/components/config-area',
                        'sortOrder' => $sortOrder,
                        'dataScope' => 'name',
                        'fileInputName' => 'color',
                        'flashvars' => $this->getFlashVars(),
                    ],
                ],
            ],
            'children' => [],
        ];
    }

    protected function getFlashVars()
    {
        $product = $this->locator->getProduct();
        $productId = $product->getId();
        $noOfSides = $product->getNoOfSides();
        $falshVars = "noofside=".$noOfSides."&prodid=".$productId."&ismap=0";
        return $falshVars;
    }


    protected function get3DMapImageContainer($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Fieldset::NAME,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' => $sortOrder,
                        'additionalClasses' => 'admin__field-wide',
                    ],
                ],
            ],
            'children' => [
                self::CODE_MAP_IMAGE => $this->getMapImageFieldConfig(20),
                //'model_3d' => $this->get3DModelConfig(20)
            ],
        ];
    }

    protected function get3DModelContainer($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Fieldset::NAME,
                        'componentType' => Fieldset::NAME,
                        'sortOrder' => $sortOrder,
                        'additionalClasses' => 'admin__field-wide',
                    ],
                ],
            ],
            'children' => [
                //'map_image' => $this->getMapImageFieldConfig(20),
                self::CODE_MODEL_3D => $this->get3DModelConfig(20)
            ],
        ];
    }

    protected function getMapImageFieldConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'label' => __('Map Image'),
                        'dataScope' => '',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'image' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Map Image'),
                                'formElement' => 'fileUploader',
                                'componentType' => 'fileUploader',
                                'component' => 'Designnbuy_Threed/js/components/file-uploader',
                                'elementTmpl' => 'Designnbuy_Threed/components/file-uploader',
                                'fileInputName' => self::CODE_MAP_IMAGE,
                                'uploaderConfig' => [
                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                        'threed/preview_image/upload',
                                        ['type' => self::CODE_MAP_IMAGE, '_secure' => true]
                                    ),
                                ],
                                'dataScope' => self::CODE_MAP_IMAGE.'.file',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function get3DModelConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Container::NAME,
                        'formElement' => Container::NAME,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'label' => __('3D Model'),
                        'dataScope' => '',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'image' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('3D Model'),
                                'formElement' => 'fileUploader',
                                'componentType' => 'fileUploader',
                                'component' => 'Designnbuy_Threed/js/components/file-uploader',
                                'elementTmpl' => 'Designnbuy_Threed/components/obj-uploader',
                                'fileInputName' => self::CODE_MODEL_3D,
                                'uploaderConfig' => [
                                    'url' => $this->urlBuilder->addSessionParam()->getUrl(
                                        'threed/preview_image/upload',
                                        ['type' => self::CODE_MODEL_3D, '_secure' => true]
                                    ),
                                ],
                                'dataScope' => self::CODE_MODEL_3D.'.file',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


    protected function getPanelChildren()
    {
        return [
            'threed_configuration_products_button_set' => $this->getButtonSet()

        ];
    }

    protected function getButtonSet()
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'component' => 'Designnbuy_Merchandise/js/components/container-configarea-handler',
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content1' => __(
                            '3D Preview Configuration'
                        ),
                        'template' => 'ui/form/components/complex',
                        'createTabButton' => 'ns = ${ $.ns }, index = create_threed_configuration_products_button',
                    ],
                ],
            ],
            'children' => [

                'create_threed_configuration_products_button' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $this->dataScopeName.'.advanced_threed_modal',
                                        'actionName' => 'trigger',
                                        'params' => ['active', true],
                                    ],
                                    [
                                        'targetName' => $this->dataScopeName.'.advanced_threed_modal',
                                        'actionName' => 'openModal',
                                    ],
                                ],
                                'title' => __('3D Preview Configuration'),
                                'sortOrder' => 20,

                            ],
                        ],
                    ],
                ],
            ],
        ];
    }


}