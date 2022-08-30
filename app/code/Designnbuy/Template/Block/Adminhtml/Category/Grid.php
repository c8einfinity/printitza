<?php

namespace Designnbuy\Template\Block\Adminhtml\Category;

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
     * @var \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory
     */
    protected $_categoryCollectionFactory;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

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
     * @param \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Designnbuy\Template\Helper\Data                                  $bannertemplateHelper
     * @param \Designnbuy\Template\Model\Status                                 $status
     * @param array                                                                     $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\Template\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        //\Designnbuy\Template\Helper\Data $bannertemplateHelper,
       // \Designnbuy\Template\Model\Status $status,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($context, $backendHelper, $data);
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('categoryGrid');
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

        $collection = $this->_categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');

        $collection->joinAttribute('title','designnbuy_template_category/title','entity_id',null,'left',$adminStoreId);
        $collection->joinAttribute('status','designnbuy_template_category/status','entity_id',null,'left',$adminStoreId);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
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
        if (!$this->_isExport) {
            $this->addColumn(
                'edit',
                [
                    'header' => __('Edit'),
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => [
                        [
                            'caption' => __('Edit'),
                            'url' => [
                                'base' => '*/*/edit',
                            ],
                            'field' => 'id',
                        ],
                    ],
                    'filter' => false,
                    'sortable' => false,
                    'index' => 'stores',
                    'header_css_class' => 'col-action',
                    'column_css_class' => 'col-action',
                ]
            );
        }
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
        $this->getMassactionBlock()->setFormFieldName('category');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('template/*/massDelete'),
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

        return $this;
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
