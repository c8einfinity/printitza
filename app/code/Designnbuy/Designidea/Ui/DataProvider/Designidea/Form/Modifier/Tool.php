<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Designidea\Ui\DataProvider\Designidea\Form\Modifier;

use Designnbuy\Designidea\Model\Locator\LocatorInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Store\Api\GroupRepositoryInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\DynamicRows;
use Magento\Framework\UrlInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Websites customizes websites panel
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Tool extends AbstractModifier
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
     * Catalog category factory
     *
     * @var \Designnbuy\Merchandise\Model\MerchandiseFactory
     */
    protected $_merchandise;

    protected $productRepository;

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
        ArrayManager $arrayManager,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->locator = $locator;
        $this->storeManager = $storeManager;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->storeRepository = $storeRepository;
        $this->urlBuilder = $urlBuilder;
        $this->arrayManager = $arrayManager;
        $this->_merchandise = $merchandise;
        $this->productRepository = $productRepository;
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
        if (!$this->locator->getDesignIdea()->getId()) {
            return $meta;
        }
        $meta = array_replace_recursive(
            $meta,
            [
                'tool' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'additionalClasses' => 'admin__fieldset-product-websites',
                                'label' => __('Create Design'),
                                'collapsible' => true,
                                'componentType' => Form\Fieldset::NAME,
                                'dataScope' => self::DATA_SCOPE_DESIGNIDEA,
                                'disabled' => false,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $meta,
                                    'design-tool-settings',
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
                                                'targetName' => 'designidea_form.designidea_form.create_tool_modal',
                                                'actionName' => 'toggleModal',
                                            ],
                                            [
                                                'targetName' => 'designidea_form.designidea_form.create_tool_modal.create_tool_container',
                                                'actionName' => 'render'
                                            ],
                                            [
                                                'targetName' => 'designidea_form.designidea_form.create_tool_modal.create_tool_container',
                                                'actionName' => 'resetForm'
                                            ]
                                        ],
                                        'additionalForGroup' => true,
                                        'provider' => false,
                                        'source' => 'designidea_details',
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
                                'title' => __('Create Design'),
                            ]
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
                                            'handle' => 'designidea_create_tool',
                                            'designidea_id' => $this->locator->getDesignIdea()->getId(),
                                            'store' => $this->locator->getStore()->getId(),
                                            'id' => $this->getDefaultProduct(),//product Id
                                            'buttons' => 1
                                        ]
                                    ),
                                    'autoRender' => false,
                                    'ns' => 'designidea_create_tool_form',
                                    'externalProvider' => 'designidea_create_tool_form.designidea_create_tool_form_data_source',
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

    protected function getDefaultProduct()
    {
        $productId = $this->locator->getDesignIdea()->getProductId();
        if($productId == '') {
            return $this->_merchandise->getDefaultProduct();
        }
        try {
            $product = $this->productRepository->getById($productId);
        }  catch (\Magento\Framework\Exception\NoSuchEntityException $e){
            $product = false;
        }

        if(!$product) {
            $productId = $this->_merchandise->getDefaultProduct();
        }
        return $productId;
    }
}
