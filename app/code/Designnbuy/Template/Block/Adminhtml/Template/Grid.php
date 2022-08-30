<?php

namespace Designnbuy\Template\Block\Adminhtml\Template;

/**
 * Template grid.
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Template collection factory.
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_templateCollectionFactory;

    /**
     * Template collection factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit
     */
    protected $_baseUnitModel;

    /**
     * @var \Magento\Store\Model\WebsiteFactory
     */
    protected $_websiteFactory;

    /**
     * Template collection factory
     *
     * @var \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * Helper.
     *
     * @var \Designnbuy\Template\Helper\Data
     */
    //protected $_bannertemplateHelper;

    /**
     * Available status.
     *
     * @var \Designnbuy\Template\Model\Status
     */
   // private $_status;

    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context                                   $context
     * @param \Magento\Backend\Helper\Data                                              $backendHelper
     * @param \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
     * @param \Designnbuy\Template\Helper\Data                                  $bannertemplateHelper
     * @param \Designnbuy\Template\Model\Status                                 $status
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteFactory,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit $baseUnit,
        \Magento\Store\Api\WebsiteRepositoryInterface $websiteRepository,
        //\Designnbuy\Template\Helper\Data $bannertemplateHelper,
       // \Designnbuy\Template\Model\Status $status,
        array $data = []
    ) {
        $this->_websiteFactory = $websiteFactory;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
        $this->_templateCollectionFactory = $templateCollectionFactory;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_templateFactory = $templateFactory;
        $this->_baseUnitModel = $baseUnit;
        $this->websiteRepository = $websiteRepository;
       // $this->_bannertemplateHelper = $bannertemplateHelper;
       // $this->_status = $status;
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('template_grid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * @return Store
     */
    protected function _getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        return $this->_storeManager->getStore($storeId);
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


        $typeId = ['template', 'template'];

        $collection = $this->_templateCollectionFactory->create();
        $collection->addTemplateFilter();
        //$collection->addAttributeToSelect('*');
        $collection->addAttributeToSelect('title');
        $collection->addAttributeToSelect('status');
        $collection->addAttributeToSelect('category_id');
        $collection->addAttributeToSelect('image');
        $collection->addAttributeToSelect('unit');
        $collection->addAttributeToSelect('no_of_pages');
        $collection->addAttributeToSelect('width');
        $collection->addAttributeToSelect('height');
        $collection->addAttributeToSelect('identifier');
        /*$collection->setStoreId($adminStoreId);*/
        $collection->joinAttribute('title','designnbuy_template/title','entity_id',null,'left',$adminStoreId);
        $collection->joinAttribute('status','designnbuy_template/status','entity_id',null,'left',$adminStoreId);
        //$collection->joinAttribute('marketplace_status','designnbuy_template/marketplace_status','entity_id',null,'left',$adminStoreId);

        $this->setCollection($collection);
        parent::_prepareCollection();
        $this->getCollection()->addWebsiteNamesToResult();
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
                    'designnbuy_template_website',
                    'website_id',
                    'template_id=entity_id',
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
            'identifier',
            [
                'header' => __('URL Key'),
                'index'  => 'identifier',
                'class'  => 'xxx',
                'width'  => '50px',
            ]
        );
        
        $this->addColumn(
            'category_id',
            [
                'header' => __('Category'),
                'sortable' => true,
                'index' => 'category_id',
                'type' => 'options',
                'renderer'  => 'Designnbuy\Template\Block\Adminhtml\Template\Grid\Renderer\Category',
                'options' => $this->_categoryCollectionFactory->create()->addAttributeToSelect('title')->setOrder('title','asc')->toOptionHash(),
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

        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'sortable' => false,
                'index' => 'image',
                'filter'   => false,
                'renderer'  => 'Designnbuy\Template\Block\Adminhtml\Template\Grid\Renderer\Image'

            ]
        );

        $this->addColumn(
            'unit',
            [
                'header' => __('Unit'),
                'index'  => 'unit',
                'class'  => 'xxx',
                'width'  => '50px',
                'type'  => 'options',
                'options' => $this->_baseUnitModel->getOptionArray()
            ]
        );

        $this->addColumn(
            'no_of_pages',
            [
                'header' => __('No. of Pages'),
                'index'  => 'no_of_pages',
                'type'=> 'number',

            ]
        );

        $this->addColumn(
            'width',
            [
                'header' => __('Width'),
                'index'  => 'width',
                'type'=> 'number',

            ]
        );

        $this->addColumn(
            'height',
            [
                'header' => __('Height'),
                'index'  => 'height',
                'type'=> 'number',

            ]
        );

        /*$this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'index'  => 'position',
                'type'=> 'number',

            ]
        );*/

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'websites',
                [
                    'header' => __('Websites'),
                    'sortable' => false,
                    'index' => 'websites',
                    'type' => 'options',
//                    'options' => $this->_websiteFactory->create()->load()->toOptionHash(),
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
                'header'  => __('Download Template'),
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => [
                    [
                        'caption' => __('Download Template'),
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

        $this->_eventManager->dispatch('template_grid_build', ['grid' => $this]);
        $this->_eventManager->dispatch('remove_template_grid_build', ['grid' => $this]);
        //$this->addExportType('*/*/exportCsv', __('CSV'));
        //$this->addExportType('*/*/exportXml', __('XML'));
        //$this->addExportType('*/*/exportExcel', __('Excel'));
        return parent::_prepareColumns();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('entity_id');
        $this->getMassactionBlock()->setFormFieldName('template');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('template/*/massDelete'),
                'confirm' => __('Are you sure?'),
            ]
        );

//        $this->getMassactionBlock()->addItem(
//            'identifier',
//            [
//                'label' => __('Update Identifier'),
//                'url' => $this->getUrl('template/*/massIdentifier'),
//                'confirm' => __('Are you sure you want to update?'),
//            ]
//        );

        $status = array();
        $status = [
            ['value' => '', 'label' => ''],
            ['value' => 1, 'label' => 'Enabled'],
            ['value' => 0, 'label' => 'Disabled'],
        ];

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change Status'),
                'url' => $this->getUrl('template/*/massStatus', ['_current' => true]),
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
                'url' => $this->getUrl('template/*/massCategory', ['_current' => true]),
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

        $this->_eventManager->dispatch('template_grid_build', ['grid' => $this]);
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
}
