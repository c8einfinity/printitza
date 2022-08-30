<?php
namespace Designnbuy\CustomerPhotoAlbum\Block;

class CreateAlbum extends \Magento\Framework\View\Element\Template
{

    const DELETE_URL = 'photoalbum/album/delete';

    /**
     * @var string
     */
    protected $_collectionFactory;

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
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\Collection $collection,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_collectionFactory = $collection;
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
        $this->setTemplate('Designnbuy_CustomerPhotoAlbum::create.phtml');
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

    public function getPhotoAlbumImageDir($image)
	{
        return $this->_mediaDirectory->getAbsolutePath('customer/album/').$image;
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
