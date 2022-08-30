<?php
namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Album;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\albumFactory
     */
    protected $_albumFactory;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\CustomerPhotoAlbum\Model\albumFactory $albumFactory
     * @param \Designnbuy\CustomerPhotoAlbum\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $AlbumFactory,
        \Designnbuy\CustomerPhotoAlbum\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_albumFactory = $AlbumFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('album_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_albumFactory->getCollection();
        $collection->addFieldToFilter("customer_id","999999999");
        
        $collection->addStoreData();
        
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'album_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'album_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
        $this->addColumn(
            'title',
            [
                'header' => __('Album Title'),
                'index' => 'title',
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                ['header' => __('Store View'), 'index' => 'store_id', 'type' => 'store', 'store_view' => true]
            );
        }
				
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => $this->status()
            ]
        );
				


		
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
                            'base' => '*/*/edit'
                        ],
                        'field' => 'album_id'
                    ]
                ],
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'header_css_class' => 'col-action',
                'column_css_class' => 'col-action'
            ]
        );
		

		
		   $this->addExportType($this->getUrl('customerphotoalbum/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('customerphotoalbum/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('album_id');
        //$this->getMassactionBlock()->setTemplate('Designnbuy_CustomerPhotoAlbum::album/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('album');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('customerphotoalbum/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('customerphotoalbum/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		
    protected function status(){
        return ['1'=>__('Enable'), '0' =>__('Disable')];
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customerphotoalbum/*/index', ['_current' => true]);
    }

    /**
     * @param \Designnbuy\CustomerPhotoAlbum\Model\album|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'customerphotoalbum/*/edit',
            ['album_id' => $row->getId()]
        );
		
    }

	

}