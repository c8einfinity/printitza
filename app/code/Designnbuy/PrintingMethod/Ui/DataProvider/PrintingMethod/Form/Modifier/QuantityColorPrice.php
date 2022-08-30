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
use Magento\Ui\Component\Form\Element\Select;
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
class QuantityColorPrice implements ModifierInterface
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
     * QuantityRange factory
     *
     * @var \Designnbuy\PrintingMethod\Model\QuantityRangeFactory
     */
    protected $_quantityRangeFactory;

    /**
     * ColorCounter factory
     *
     * @var \Designnbuy\PrintingMethod\Model\ColorCounterFactory
     */
    protected $_colorCounterFactory;

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
        \Designnbuy\PrintingMethod\Model\QuantityRangeFactory $_quantityRangeFactory,
        \Designnbuy\PrintingMethod\Model\ColorCounterFactory $_colorCounterFactory
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->moduleManager = $moduleManager;
        $this->directoryHelper = $directoryHelper;
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->_quantityRangeFactory = $_quantityRangeFactory;
        $this->_colorCounterFactory = $_colorCounterFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'quantity_color_price' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Quantity Colors Price'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => '',
                                'sortOrder' => 110,
                                'visibleByPricingLogic' => 2,
                                'dependsOn' => 'pricing_logic',
                            ],
                        ],
                    ],
                    'children' => [
                        'qcprice' => $this->getGridConfig(10),
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
                        'deleteProperty' => 'is_delete',
                        'deleteValue' => '1',
                        'renderDefaultRecord' => false,
                        'sortOrder' => $sortOrder,
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
                        $this->getQuantityRangeFieldConfig(10),
                        $this->getNoOfColorFieldConfig(20),
                        $this->getSidesFieldConfig(100),
                        $this->getDeleteAction(30),
                        $this->getPosition(30)
                    ),
                ],
            ],
        ];
    }

    protected function getQuantityRangeFieldConfig($sortOrder)
    {
        return [
            'quantityrange_id' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Qty Range'),
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => 'quantityrange_id',
                            'dataType' => Text::NAME,
                            'options' => $this->getQuantityRange(),
                            'disableLabel' => true,
                            'multiple' => false,
                            'selectedPlaceholders' => [
                                'defaultPlaceholder' => __('-- Please select --'),
                            ],
                            'validation' => [
                                'required-entry' => true
                            ],
                            'sortOrder' => $sortOrder,
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getQuantityRange()
    {
        $collection = $this->_quantityRangeFactory->create()->getResourceCollection();
        $options = $collection->toOptionArray();
        return $options;
    }

    protected function getNoOfColorFieldConfig($sortOrder)
    {
        return [
            'colorcounter_id' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'label' => __('Number of Colors'),
                            'componentType' => Field::NAME,
                            'formElement' => Select::NAME,
                            'dataScope' => 'colorcounter_id',
                            'dataType' => Text::NAME,
                            'options' => $this->getNoOfColor(),
                            'disableLabel' => true,
                            'multiple' => false,
                            'selectedPlaceholders' => [
                                'defaultPlaceholder' => __('-- Please select --'),
                            ],
                            'validation' => [
                                'required-entry' => true
                            ],
                            'sortOrder' => $sortOrder,
                        ],
                    ],
                ],
            ],
        ];
    }
    protected function getNoOfColor()
    {
        $collection = $this->_colorCounterFactory->create()->getResourceCollection();
        $options = $collection->toOptionArray();
        return $options;
    }

    protected function getSidesFieldConfig($sortOrder)
    {
        return $this->_createSidesField($sortOrder);
    }

    protected function _createSidesField($sortOrder)
    {
        $sides = \Designnbuy\Merchandise\Helper\Data::SIDES;
        $sideFields = [];
        //$sortOrder = 100;
        $field = 0;
        for($field = 0; $field < $sides; $field++){
            $sideFields['side'.$field] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' =>  __('Side') . ($field + 1),
                            'dataScope' => $field,
                            'validation' => [
                                 'required-entry' => true
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
        return $sideFields;
    }

    protected function getDeleteAction($sortOrder)
    {
        return [
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => '',
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
