<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class Move extends \Magento\Framework\App\Action\Action
{
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    protected $_album;
    protected $_albumFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;
    private $resultJsonFactory;
    
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	protected $date;
    protected $timezone;
    protected $_photos;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $albumFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        ResourceConnection $resource,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    )
    {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_customerSession = $customerSession->create();
		$this->date = $date;
        $this->timezone = $timezone;
        $this->_albumFactory = $albumFactory;
        $this->_album = $album;
        $this->_photos = $photos;
        $this->resultJsonFactory=$resultJsonFactory;
        $this->resource = $resource;
        return parent::__construct($context);
    }

    public function execute()
    {	
        $request = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        if(isset($request["album_id"])){
            if(isset($request["move_album"])){
                $_moveAlbumId = $request["move_album"];
                $_albumPhotos = $this->_photos->getCollection();
                $_albumPhotos->addFieldToFilter('photo_id',$request["photos"]);
                try{
                    foreach ($_albumPhotos as $photo) {
                        $photo->setAlbumId($_moveAlbumId);
                        $photo->save();
                    }
                    return $resultJson->setData(
                        [
                            'moved' => true,
                        ]
                    );
                }catch (\Exception $e){
                    return $resultJson->setData(
                        [
                            'moved' => false
                        ]
                    );
                }
            }
        }
        return $resultJson->setData(
            [
                'moved' => false
            ]
        );
    }
	
    public function getCustomerSession()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getCustomerId(){
        $customer = $this->getCustomerSession();
        return $customer->getId();
    }
}