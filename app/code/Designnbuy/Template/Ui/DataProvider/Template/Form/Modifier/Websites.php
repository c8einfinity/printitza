<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Template\Ui\DataProvider\Template\Form\Modifier;

use Designnbuy\Template\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Store\Model\ResourceModel\Website\Collection;
/**
 * Class Websites customizes websites panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Websites extends AbstractModifier
{
    const SORT_ORDER = 500;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var \Magento\Store\Api\WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    /**
     * @var \Magento\Store\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Store\Api\StoreRepositoryInterface
     */
    protected $storeRepository;

    /**
     * @var array
     */
    protected $websitesOptionsList;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $websitesList;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var \Magento\Store\Model\ResourceModel\Website\CollectionFactory
     */
    protected $_websitesFactory;

    /**
     * @param LocatorInterface $locator
     * @param StoreManagerInterface $storeManager
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(
        LocatorInterface $locator,
        StoreManagerInterface $storeManager,
        WebsiteRepositoryInterface $websiteRepository,
        GroupRepositoryInterface $groupRepository,
        StoreRepositoryInterface $storeRepository,
        UrlInterface $urlBuilder,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websitesFactory,
        ArrayManager $arrayManager
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->storeRepository = $storeRepository;
        $this->urlBuilder = $urlBuilder;
        $this->_websitesFactory = $websitesFactory;
        $this->arrayManager = $arrayManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $modelId = $this->locator->getTemplate()->getId();

        if (!$this->storeManager->isSingleStoreMode() && $modelId) {
            $websiteIds = $this->getWebsitesValues();
            foreach ($this->getWebsitesList() as $website) {
                if (!in_array($website['id'], $websiteIds) && $website['storesCount']) {
                    $data[$modelId]['product']['copy_to_stores'][$website['id']] = [];
                    foreach ($website['groups'] as $group) {
                        foreach ($group['stores'] as $storeView) {
                            $data[$modelId]['product']['copy_to_stores'][$website['id']][] = [
                                'storeView' => $storeView['name'],
                                'copy_from' => 0,
                                'copy_to' => $storeView['id'],
                            ];
                        }
                    }
                }
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if (!$this->storeManager->isSingleStoreMode()) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'websites' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalClasses' => 'admin__fieldset-product-websites',
                                    'label' => __('Assign to Websites'),
                                    'collapsible' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'dataScope' => self::DATA_SCOPE_TEMPLATE,
                                    'disabled' => false,
                                    'sortOrder' => $this->getNextGroupSortOrder(
                                        $meta,
                                        'template-settings',
                                        self::SORT_ORDER
                                    )
                                ],
                            ],
                        ],
                        'children' => $this->getFieldsForFieldset(),
                    ],
                ]
            );
        }
        if ($this->locator->getTemplate()->getId()) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'tool' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'additionalClasses' => 'admin__fieldset-product-websites',
                                    'label' => __('Template Builder'),
                                    'collapsible' => true,
                                    'componentType' => Form\Fieldset::NAME,
                                    'dataScope' => self::DATA_SCOPE_TEMPLATE,
                                    'disabled' => false,
                                    'sortOrder' => $this->getNextGroupSortOrder(
                                        $meta,
                                        'template-settings',
                                        self::SORT_ORDER
                                    )
                                ],
                            ],
                        ],
                        'children' => [
                            'create_tool_button' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'title' => __('Create Design'),
                                            'formElement' => 'container',
                                            'additionalClasses' => 'admin__field-small',
                                            'componentType' => 'container',
                                            'component' => 'Magento_Ui/js/form/components/button',
                                            'template' => 'ui/form/components/button/container',
                                            'actions' => [
                                                [
                                                    'targetName' => 'template_form.template_form.create_tool_modal',
                                                    'actionName' => 'toggleModal',
                                                ],
                                                [
                                                    'targetName' =>
                                                        'template_form.template_form.create_tool_modal.create_tool_container',
                                                    'actionName' => 'render'
                                                ],
                                                [
                                                    'targetName' =>
                                                        'template_form.template_form.create_tool_modal.create_tool_container',
                                                    'actionName' => 'resetForm'
                                                ]
                                            ],
                                            'additionalForGroup' => true,
                                            'provider' => false,
                                            'source' => 'template_details',
                                            'displayArea' => 'insideGroup',
                                            'sortOrder' => 20,
                                        ],
                                    ],
                                ]
                            ],
                        ],
                        // 'children' => $this->getCreateToolFieldset($meta),
                    ],
                ]
            );

            $meta = $this->getCreateToolFieldset($meta);
        }

        return $meta;
    }



    /**
     * Create slide-out panel for new category creation
     *
     * @param array $meta
     * @return array
     */
    protected function getCreateToolFieldset(array $meta)
    {

        return $this->arrayManager->set(
            'create_tool_modal',
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'isTemplate' => false,
                            'componentType' => 'modal',
                            'options' => [
                                'title' => __('Create Template ('.$this->locator->getTemplate()->getTitle().')'),
                            ],
                            'imports' => [
                                'state' => '!index=create_category:responseStatus'
                            ],
                        ],
                    ],
                ],
                'children' => [
                    'create_tool_container' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'label' => '',
                                    'componentType' => 'container',
                                    'component' => 'Magento_Ui/js/form/components/insert-form',
                                    'dataScope' => '',
                                    'update_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                    'render_url' => $this->urlBuilder->getUrl(
                                        'mui/index/render_handle',
                                        [
                                            'handle' => 'template_create_tool',
                                            'id' => $this->locator->getTemplate()->getId(),
                                            'store' => $this->locator->getStore()->getId(),
                                            'buttons' => 1
                                        ]
                                    ),
                                    'autoRender' => false,
                                    'ns' => 'template_create_tool_form',
                                    'externalProvider' => 'template_create_tool_form.template_create_tool_form_data_source',
                                    'toolbarContainer' => '${ $.parentName }',
                                    'formSubmitType' => 'ajax',
                                ],
                            ],
                        ]
                    ]
                ]
            ]
        );
    }


    /**
     * Prepares children for the parent fieldset
     *
     * @return array
     */
    protected function getFieldsForFieldset()
    {
        $children = [];
        $websiteIds = $this->getWebsitesValues();
        $websitesList = $this->getWebsitesList();

        $isNewProduct = !$this->locator->getTemplate()->getId();
        $tooltip = [
            'link' => 'http://docs.magento.com/m2/ce/user_guide/configuration/scope.html',
            'description' => __(
                'If your Magento site has multiple views, ' .
                'you can set the scope to apply to a specific view.'
            ),
        ];
        $sortOrder = 0;
        $label = __('Websites');

        $defaultWebsiteId = $this->websiteRepository->getDefault()->getId();
        foreach ($websitesList as $website) {
            $isChecked = in_array($website['id'], $websiteIds)
                || ($defaultWebsiteId == $website['id'] && $isNewProduct);
            $children[$website['id']] = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Form\Element\DataType\Number::NAME,
                            'componentType' => Form\Field::NAME,
                            'formElement' => Form\Element\Checkbox::NAME,
                            'description' => __($website['name']),
                            'tooltip' => $tooltip,
                            'sortOrder' => $sortOrder,
                            'dataScope' => 'website_ids.' . $website['id'],
                            'label' => $label,
                            'valueMap' => [
                                'true' => (string)$website['id'],
                                'false' => '',
                            ],
                            'validation' => [
                                'required-entry' => count($websitesList) == 1 ? true : false,

                            ],
                            //'value' => $isChecked ? (string)$website['id'] : '0',
                            'value' => $isChecked ? (string)$website['id'] : (count($websitesList) == 1 ? '' : '0'),
                        ],
                    ],
                ],
            ];

            $sortOrder++;
            $tooltip = null;
            $label = ' ';

            if (!$isNewProduct && !in_array($website['id'], $websiteIds) && $website['storesCount']) {
                //$children['copy_to_stores.' . $website['id']] = $this->getDynamicRow($website['id'], $sortOrder);
                $sortOrder++;
            }
        }

        return $children;
    }

    /**
     * Prepares dynamic rows configuration
     *
     * @param int $websiteId
     * @param int $sortOrder
     * @return array
     */
    protected function getDynamicRow($websiteId, $sortOrder)
    {
        $configRow = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => DynamicRows::NAME,
                        'label' => ' ',
                        'renderDefaultRecord' => true,
                        'addButton' => false,
                        'columnsHeader' => true,
                        'dndConfig' => ['enabled' => false],
                        'imports' => [
                            'visible' => '${$.namespace}.${$.namespace}.websites.' . $websiteId . ':checked'
                        ],
                        'itemTemplate' => 'record',
                        'dataScope' => '',
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => $websiteId,
                            ],
                        ],
                    ],
                    'children' => [
                        'storeView' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => Form\Field::NAME,
                                        'formElement' => Form\Element\Input::NAME,
                                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'dataScope' => 'storeView',
                                        'label' => __('Store View'),
                                        'fit' => true,
                                        'sortOrder' => 0,
                                    ],
                                ],
                            ],
                        ],
                        'copy_from' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Form\Element\DataType\Text::NAME,
                                        'formElement' => Form\Element\Select::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'component' => 'Magento_Ui/js/form/element/ui-select',
                                        'elementTmpl' => 'ui/grid/filters/elements/ui-select',
                                        'disableLabel' => true,
                                        'filterOptions' => false,
                                        'selectType' => 'optgroup',
                                        'multiple' => false,
                                        'dataScope' => 'copy_from',
                                        'label' => __('Copy Data from'),
                                        'options' => $this->getWebsitesOptions(),
                                        'sortOrder' => 1,
                                        'selectedPlaceholders' => [
                                            'defaultPlaceholder' => __('Default Values'),
                                        ],
                                    ],
                                ],
                            ]
                        ],
                        'copy_to' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'dataType' => Form\Element\DataType\Number::NAME,
                                        'formElement' => Form\Element\Hidden::NAME,
                                        'componentType' => Form\Field::NAME,
                                        'dataScope' => 'copy_to',
                                    ],
                                ],
                            ]
                        ],
                    ],
                ],
            ],
        ];
        return $configRow;
    }

    /**
     * Manage options list for selects
     *
     * @return array
     */
    protected function getWebsitesOptions()
    {
        if (!empty($this->websitesOptionsList)) {
            return $this->websitesOptionsList;
        }
        return $this->websitesOptionsList = $this->getWebsitesOptionsList();
    }

    /**
     * @return array
     */
    protected function getWebsitesOptionsList()
    {
        $options = [
            [
                'value' => '0',
                'label' => __('Default Values'),
            ],
        ];
        $websitesList = $this->getWebsitesList();
        $websiteIds = $this->getWebsitesValues();
        foreach ($websitesList as $website) {
            if (!in_array($website['id'], $websiteIds)) {
                continue;
            }
            $websiteOption = [
                'value' => '0.' . $website['id'],
                'label' => __($website['name']),
            ];
            $groupOptions = [];
            foreach ($website['groups'] as $group) {
                $groupOption = [
                    'value' => '0.' . $website['id'] . '.' . $group['id'],
                    'label' => __($group['name']),
                ];
                $storeViewOptions = [];
                foreach ($group['stores'] as $storeView) {
                    $storeViewOptions[] = [
                        'value' => $storeView['id'],
                        'label' => __($storeView['name']),
                    ];
                }
                if (!empty($storeViewOptions)) {
                    $groupOption['optgroup'] = $storeViewOptions;
                    $groupOptions[] = $groupOption;
                } else {
                    $groupOption = null;
                }
            }
            if (!empty($groupOptions)) {
                $websiteOption['optgroup'] = $groupOptions;
                $options[] = $websiteOption;
            } else {
                $websiteOption = null;
            }
        }
        return $options;
    }

    /**
     * Prepares websites list with groups and stores as array
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function getWebsitesList()
    {
        if (!empty($this->websitesList)) {
            return $this->websitesList;
        }
        $this->websitesList = [];
        $groupList = $this->groupRepository->getList();
        $storesList = $this->storeRepository->getList();
        //$websites = $this->_websitesFactory->create()->load();

        foreach ($this->websiteRepository->getList() as $website) {
        //foreach ($websites as $website) {
            $websiteId = $website->getId();
            if (!$websiteId) {
                continue;
            }
            $websiteRow = [
                'id' => $websiteId,
                'name' => $website->getName(),
                'storesCount' => 0,
                'groups' => [],
            ];
            foreach ($groupList as $group) {
                $groupId = $group->getId();
                if (!$groupId || $group->getWebsiteId() != $websiteId) {
                    continue;
                }
                $groupRow = [
                    'id' => $groupId,
                    'name' => $group->getName(),
                    'stores' => [],
                ];
                foreach ($storesList as $store) {
                    $storeId = $store->getId();
                    if (!$storeId || $store->getStoreGroupId() != $groupId) {
                        continue;
                    }
                    $websiteRow['storesCount']++;
                    $groupRow['stores'][] = [
                        'id' => $storeId,
                        'name' => $store->getName(),
                    ];
                }
                $websiteRow['groups'][] = $groupRow;
            }
            $this->websitesList[] = $websiteRow;
        }

        return $this->websitesList;
    }

    /**
     * Return array of websites ids, assigned to the template
     *
     * @return array
     */
    protected function getWebsitesValues()
    {
        return $this->locator->getTemplateWebsiteIds();
    }
}
