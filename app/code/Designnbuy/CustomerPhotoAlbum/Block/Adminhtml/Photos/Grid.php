<?php
namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\photosFactory
     */
    protected $_photosFactory;
    
    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album
     */
    protected $album;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\CustomerPhotoAlbum\Model\photosFactory $photosFactory
     * @param \Designnbuy\CustomerPhotoAlbum\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\CustomerPhotoAlbum\Model\PhotosFactory $PhotosFactory,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $AlbumFactory,
        \Designnbuy\CustomerPhotoAlbum\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_photosFactory = $PhotosFactory;
        $this->album = $AlbumFactory;
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
        $this->setDefaultSort('photo_id');
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
        $collection = $this->_photosFactory->create()->getCollection();
        
        $joinConditions = 'u.album_id = main_table.album_id';

        $collection->getSelect()->join(
            ['u' => $collection->getTable('designnbuy_customer_album')],
            $joinConditions,
            ['customer_id']
        );
        $collection->addFieldToFilter("customer_id","999999999");
        
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
            'photo_id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'photo_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
		
        $this->addColumn(
            'album_id',
            [
                'header' => __('Album Title'),
                'index' => 'main_table.album_id',
                'type'    => 'options',
                'options' => $this->getAdminAlbum(),
                'renderer'  => 'Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Grid\Renderer\Album'
            ]
        );
        
        $this->addColumn(
            'image',
            [
                'header' => __('Image'),
                'sortable' => false,
                'index' => 'image',
                'filter'   => false,
                'renderer'  => 'Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Grid\Renderer\Image'

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
                        'field' => 'photo_id'
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

        $this->setMassactionIdField('photo_id');
        //$this->getMassactionBlock()->setTemplate('Designnbuy_CustomerPhotoAlbum::photos/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('photos');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('customerphotoalbum/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );
        
        $this->getMassactionBlock()->addItem(
            'changealbum',
            [
                'label' => __('Change Album'),
                'url' => $this->getUrl('customerphotoalbum/*/massAlbum'),
                'confirm' => __('Are you sure?')
            ]
        );

        $albumCollection = $this->album->getCollection()->addFieldToFilter("customer_id","999999999");
        $statuses = [];
        foreach($albumCollection as $albumDt){
            $statuses[$albumDt->getAlbumId()] = $albumDt->getTitle();
        }
        $this->getMassactionBlock()->addItem(
            'changealbum',
            [
                'label' => __('Change Album'),
                'url' => $this->getUrl('customerphotoalbum/*/massAlbum', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'changealbum',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Album'),
                        'values' => $statuses
                    ]
                ]
            ]
        );

        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('customerphotoalbum/*/index', ['_current' => true]);
    }

    /**
     * @param \Designnbuy\CustomerPhotoAlbum\Model\photos|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'customerphotoalbum/*/edit',
            ['photo_id' => $row->getId()]
        );
		
    }
    public function getAdminAlbum()
    {
        $adminAlbumCollection = $this->album->getCollection();
        $adminAlbumCollection->addFieldToSelect("album_id");
        $adminAlbumCollection->addFieldToSelect("title");
        $adminAlbumCollection->addFieldToFilter("customer_id","999999999");
        $adminAlbumCollection->setOrder('title','ASC');
        $albumList = array();
        foreach($adminAlbumCollection as $adminAlbumItem){
            $albumList[$adminAlbumItem->getAlbumId()] = $adminAlbumItem->getTitle();
        }
        return $albumList;
    }
	

}