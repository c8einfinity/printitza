<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\PrintingMethod\Ui\DataProvider\PrintingMethod\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Directory\Helper\Data;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Hidden;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
/**
 * Class AdvancedPricing
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PrintableColors implements ModifierInterface
{
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
     * @var StoreFactory
     */
    protected $storeFactory;

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
    * @var \Magento\Store\Model\StoreFactory
    */
    protected $_storeFactory;
    /**
     * Store manager
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigArea\Source\ConfigArea
     */
    protected $configArea;

    private $dataScopeName;
    /**
     * @param LocatorInterface $locator
     * @param \Magento\Store\Model\StoreFactory $storeFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ModuleManager $moduleManager
     * @param Data $directoryHelper
     * @param ArrayManager $arrayManager
     * @param string $scopeName
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        LocatorInterface $locator,
        \Magento\Store\Model\StoreFactory $_storeFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ModuleManager $moduleManager,
        Data $directoryHelper,
        ArrayManager $arrayManager,
        UrlInterface $urlBuilder
    ) {
        $this->locator = $locator;
        $this->_storeFactory = $_storeFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
        $meta = array_replace_recursive(
            $meta,
            [
                'printablecolors' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Printable Colors'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => '',
                                'sortOrder' => 100,
                                'visibleByPrintableColors' => 2,
                                'dependsOnPrintableColors' => 'printable_colors',
                            ],
                        ],
                    ],
                    'children' => [
                        'color' => $this->getGridConfig(10),
                    ]
                ],
            ]
        );

        return $meta;
    }
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {

        return $data;
    }

    protected function getGridConfig($sortOrder) {

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'addButtonLabel' => __('Add Value'),
                        'componentType' => DynamicRows::NAME,
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows',
                        'additionalClasses' => 'admin__field-wide',
                        'deleteProperty' => 'is_color_delete',
                        'deleteValue' => '1',
                        'itemTemplate' => 'record',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
                        'columnsHeader' => true,
                        'columnsHeaderAfterRender' => true,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => Container::NAME,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'positionProvider' => 'sort_order',
                                'isTemplate' => true,
                                'is_collection' => true,
                            ],
                        ],
                    ],
                    'children' => array_merge(
                        $this->getColorId(0),
                        $this->_createStoreSpecificField(),
                        $this->getColorCodeFieldConfig(20),
                        $this->getDeleteAction(30),
                        $this->getPosition(30)
                    )
                ]
            ]
        ];
    }

    protected function getColorId($sortOrder)
    {
        return [
            'color_id' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Color Id'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => 'color_id',
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }


    protected function getColorCodeFieldConfig($sortOrder) {
        return [
            'color_code' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Color Code'),
                            'component' => 'Designnbuy_Color/js/form/element/swatch-visual',
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => 'color_code',
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function _createStoreSpecificField()
    {
        $storeFields = [];
        $sortOrder = 0;
        $stores = $this->getStores();
        foreach ($stores as $store) {
            $storeFields[$store->getCode()] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' => $store->getName(),
                            'dataScope' => $store->getId(),
                            'validation' => [
                                 'required-entry' => ($store->getId() == 0) ? true : false,
                            ],
                            'additionalClasses' => 'admin__field-small'
                            //'sortOrder' => $sortOrder,
                        ],
                    ],
                ],
            ];
            $sortOrder++;
        }


        //$storeMeta['stores']['children'] = $storeFields;
        //return $storeMeta;
        return $storeFields;
    }

    /**
     * @return mixed
     */
    public function getStores()
    {
        $stores = $this->_storeFactory->create()->getResourceCollection()->setLoadDefault(true)->load();
        return $stores;
    }

    protected function getDeleteAction($sortOrder)
    {
        return [
            'action_delete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'actionDelete',
                            //'dataType' => Text::NAME,
                            'label' => null,
                            'sortOrder' => $sortOrder,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getPosition($sortOrder)
    {
        return [
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Position'),
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataScope' => 'position',
                            'dataType' => Text::NAME,
                            'sortOrder' => $sortOrder,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
        ];
    }
}
