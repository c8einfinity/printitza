<?php

namespace Designnbuy\Designidea\Block\Adminhtml\Designidea;

/**
 * Designidea grid.
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Designidea collection factory.
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_designideaCollectionFactory;

    /**
     * Designidea collection factory.
     *
     * @var \Designnbuy\Designidea\Model\DesignideaFactory
     */
    protected $_designideaFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Designidea collection factory
     *
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * Helper.
     *
     * @var \Designnbuy\Designidea\Helper\Data
     */
    //protected $_bannerdesignideaHelper;

    /**
     * Available status.
     *
     * @var \Designnbuy\Designidea\Model\Status
     */
   // private $_status;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context                                   $context
     * @param \Magento\Backend\Helper\Data                                              $backendHelper
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
     * @param \Designnbuy\Designidea\Helper\Data                                  $bannerdesignideaHelper
     * @param \Designnbuy\Designidea\Model\Status                                 $status
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        //\Designnbuy\Designidea\Helper\Data $bannerdesignideaHelper,
       // \Designnbuy\Designidea\Model\Status $status,
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
        $this->_designideaCollectionFactory = $designideaCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_designideaFactory = $designideaFactory;
        $this->websiteRepository = $websiteRepository;
       // $this->_bannerdesignideaHelper = $bannerdesignideaHelper;
       // $this->_status = $status;
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('designideaGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare collection.
     *
     * @return [type] [description]
     */
    protected function _prepareCollection()
    {
        $adminStoreId = $this->_storeManager->getStore(\Magento\Store\Model\Store::ADMIN_CODE)->getId();
        $store = $this->_getStore();
        if ($store->getId()) {
            $adminStoreId = $store->getId();
        }
        $typeId = ['designidea', 'template'];

        $collection = $this->_designideaCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToSelect('status');
        $collection->joinAttribute('title','designnbuy_designidea/title','entity_id',null,'left',$adminStoreId);
        $collection->joinAttribute('status','designnbuy_designidea/status','entity_id',null,'left',$adminStoreId);

        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
        return $this;
    }

    protected function _setCollectionOrder($column)
    {
        $collection = $this->getCollection();
        if ($collection) {
            $columnIndex = $column->getFilterIndex() ? $column->getFilterIndex() : $column->getIndex();
            $collection->setOrder($columnIndex, strtoupper($column->getDir()));
        }
        return $this;
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($this->getCollection()) {
            if ($column->getId() == 'websites') {
                $this->getCollection()->joinField(
                    'websites',
                    'designnbuy_designidea_website',
                    'website_id',
                    'designidea_id=entity_id',
                    null,
                    'left'
                );
            }
        }
        return parent::_addColumnFilterToCollection($column);
    }


    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $websites = [];
        foreach ($this->websiteRepository->getList() as $website) {
            if($website->getWebsiteId() != 0){
                $websites[$website->getWebsiteId()] = $website->getName();
            }
        }

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'header' => __('ID'),
                'type'   => 'number',
                'index'  => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id',
            ]
        );

        $this->addColumn(
            'title',
            [
                'header' => __('Title'),
                'index'  => 'title',
                'class'  => 'xxx',
                'width'  => '50px',
            ]
        );

        $store = $this->_getStore();
        if ($store->getId()) {
            $this->addColumn(
                'title',
                [
                    'header' => __('Name in %1', $store->getName()),
                    'index' => 'title',
                    'header_css_class' => 'col-name',
                    'column_css_class' => 'col-name'
                ]
            );
        }

        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'sortable' => false,
                'index' => 'image',
                'filter'   => false,
                'renderer'  => 'Designnbuy\Designidea\Block\Adminhtml\Designidea\Grid\Renderer\Image'

            ]
        );

        $this->addColumn(
            'preview_image',
            [
                'header' => __('Preview Image'),
                'sortable' => false,
                'index' => 'preview_image',
                'filter'   => false,
                'renderer'  => 'Designnbuy\Designidea\Block\Adminhtml\Designidea\Grid\Renderer\Image'

            ]
        );

        $this->addColumn(
            'category_id',
            [
                'header' => __('Category'),
                'sortable' => true,
                'index' => 'category_id',
                'type' => 'options',
                'renderer'  => 'Designnbuy\Designidea\Block\Adminhtml\Designidea\Grid\Renderer\Category',
                'options' => $this->_categoryCollectionFactory->create()->addAttributeToSelect('title')->setOrder('title','ASC')->toOptionHash(),
                'filter_condition_callback' => [$this, '_filterCategoryCondition'],
            ]
        );

        $this->addColumn(
            'identifier',
            [
                'header' => __('URL Key'),
                'index'  => 'identifier',
                'class'  => 'xxx',
                'width'  => '50px',
            ]
        );


        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
                    //'options' => $this->_websiteFactory->create()->getCollection()->toOptionHash(),
                    'options' => $websites,
                    'header_css_class' => 'col-websites',
                    'column_css_class' => 'col-websites',
                    'is_system' => true
                ]
            );
        }

        $this->addColumn(
            'status',
            [
                'header'  => __('Status'),
                'index'   => 'status',
                'type'    => 'options',
                'options' => [
                    1  => __('Enabled'),
                    0 => __('Disabled'),
                ],
            ]
        );

        $this->addColumn(
            'created_at',
            [
                'header' => __('Created at'),
                'index'  => 'created_at',
                'width'  => '120px',
                'type'   => 'datetime',
            ]
        );
        $this->addColumn(
            'updated_at',
            [
                'header'    => __('Updated at'),
                'index'     => 'updated_at',
                'width'     => '120px',
                'type'      => 'datetime',
            ]
        );

        $this->addColumn(
            'edit',
            [
                'header'  => __('Edit'),
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => [
                    [
                        'caption' => __('Edit'),
                        'url' => [
                            'base' => '*/*/edit',
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter'   => false,
                'sortable' => false,
                'index'    => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $this->addColumn(
            'download',
            [
                'header'  => __('Download Artworks'),
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => [
                    [
                        'caption' => __('Download Artworks'),
                        'url' => [
                            'base' => '*/*/download',
                        ],
                        'field' => 'id',
                    ],
                ],
                'filter'   => false,
                'sortable' => false,
                'index'    => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action',
            ]
        );

        $this->_eventManager->dispatch('designidea_grid_build', ['grid' => $this]);
        $this->addExportType('*/*/exportCsv', __('CSV'));
        $this->addExportType('*/*/exportXml', __('XML'));
        $this->addExportType('*/*/exportExcel', __('Excel'));
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('designidea');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('designidea/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

        $status = array();
        $status = [
            ['value' => '', 'label' => ''],
            ['value' => 1, 'label' => 'Enabled'],
            ['value' => 0, 'label' => 'Disabled'],
        ];
        //array_unshift($status, ['label' => '', 'value' => '']);
        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('designidea/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name'   => 'status',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => __('Status'),
                        'values' => $status,
                    ],
                ],
            ]
        );

        $categories = $this->_categoryCollectionFactory->create()->addAttributeToSelect('title')->setOrder('title','ASC');
        $catArray = array();
        $catArray[] = array("label" => "-- Please Select a Category --", "value" => "");
        foreach($categories as $key => $category){
            $catArray[] = array("label" => $category->getTitle(),"value" => $category->getId());
        }

        $this->getMassactionBlock()->addItem(
            'category_id',
            [
                'label' => __('Change Category'),
                'url' => $this->getUrl('designidea/*/massCategory', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name'   => 'category_id',
                        'type'   => 'select',
                        'class'  => 'required-entry',
                        'label'  => __('Category'),
                        'values' => $catArray,
                    ],
                ],
            ]
        );


//        $this->getMassactionBlock()->addItem(
//            'identifier',
//            [
//                'label' => __('Update Identifier'),
//                'url' => $this->getUrl('designidea/*/massIdentifier'),
//                'confirm' => __('Are you sure you want to update?'),
//            ]
//        );
        $this->_eventManager->dispatch('designidea_grid_build', ['grid' => $this]);
        return $this;
    }

    /**
     * @return void
     */
    protected function _afterLoadCollection()
    {
        $this->getCollection()->walk('afterLoad');
        parent::_afterLoadCollection();
    }

    /**
     * @param \Magento\Framework\Data\Collection $collection
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _filterCategoryCondition($collection, $column)
    {
        if (!($value = $column->getFilter()->getValue())) {
            return;
        }

        $categoryIds[] = array('finset'=> array($value));
        $this->getCollection()->addFieldToFilter('category_id', [$categoryIds]);
    }


    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * get row url
     * @param  object $row
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl(
            '*/*/edit',
            ['id' => $row->getId()]
        );
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
    }
}
