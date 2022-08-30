<?php
namespace Designnbuy\CustomerPhotoAlbum\Block;

class Album extends \Magento\Framework\View\Element\Template
{

    const DELETE_URL = 'photoalbum/album/delete';

    /**
     * @var string
     */
    protected $_collectionFactory;

    protected $_photscollectionFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
    
    /**
     * Album Collection
     *
     * @var \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\Collection
     */
    protected $_albums;
    
    /**
     * Album Model
     *
     * @var \Designnbuy\CustomerPhotoAlbum\Model\Album
     */
    protected $_albumModel;
    
    /**
     * Media Directory 
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_mediaDirectory;


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $albumModel,
        \Magento\Store\Model\StoreManagerInterface $store_manager,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $collection,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos\CollectionFactory $photoscollection,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_collectionFactory = $collection->create();
        $this->_photscollectionFactory = $photoscollection;
        $this->_albumModel = $albumModel;
        $this->_customerSession = $customerSession;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->store_manager=$store_manager;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize Photo Album layout content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('album.phtml');
    }

    /**
     * Prepare Photo Album layout
     *
     * @return $this
     */
    /* protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getCustomerAlbums()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer.album.pager'
            )->setCollection(
                $this->getCustomerAlbums()
            );
            $this->setChild('pager', $pager);
            $this->getCustomerAlbums()->load();
        }
        return $this;
    } */

    /**
     * Get Photo Album layout data
     *
     * @return object
     */
    public function getCustomerAlbums()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->_albums) {
            $this->_albums = $this->_collectionFactory->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $this->_customerSession->getCustomer()->getId()
            )->setOrder('album_id', 'asc');
        }
        return $this->_albums;
    }

    public function getCustomerAlbumsPhotos($id)
    {
        $collection = $this->_photscollectionFactory->create();
        $collection->addFieldToFilter('album_id',$id);
        if($collection->getSize() > 0){
            return $collection->getFirstItem()->getPath();
        }
        return $collection->getFirstItem()->getPath();
    }

    public function getMediaUrl(){
        $mediaUrl = $this->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Get Photo Album layout pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get customer account back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }


    /**
     * Get post parameters for delete from cart
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return string
     */
    public function getDeletePostJson($item)
    {
        $url = $this->getUrl(self::DELETE_URL);

        $data = ['album_id' => $item->getAlbumId()];

        return json_encode(['action' => $url, 'data' => $data]);
    }

    public function getImageUrl($image)
    {
        if($image != "" && file_exists($this->getPhotoAlbumImageDir($image))){
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/uploadedImage/'.$image;
        } else {
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/uploadedImage/placeholder.png';
        }
    }

    public function getPhotoAlbumImageDir($image)
	{
        return $this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/').$image;
    }

	public function getCustomerSession()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getCustomerId(){
        $customer=$this->getCustomerSession();
        return $customer->getId();
    }
}
