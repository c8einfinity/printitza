<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class Delete extends \Magento\Framework\App\Action\Action
{
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    protected $_album;
    protected $_albumFactory;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;
	protected $date;
    protected $timezone;
    protected $_photos;
    protected $_photsFactory;
    private $resultJsonFactory;
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $albumFactory,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos\CollectionFactory $photsFactory,
        ResourceConnection $resource,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    )
    {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_customerSession = $customerSession->create();
		$this->date = $date;
        $this->timezone = $timezone;
        $this->_albumFactory = $albumFactory;
        $this->_photsFactory = $photsFactory;
        $this->_album = $album;
        $this->_photos = $photos;
        $this->resource = $resource;
        $this->resultJsonFactory=$resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {	
        $request = $this->getRequest()->getParams();
        $resultJson = $this->resultJsonFactory->create();
        if(isset($request["album_id"])) 
        {
            $albumId = $request["album_id"];
            try{
                $model = $this->_album->load($albumId);
                $collection = $this->_photsFactory->create();
                $collection->addFieldToFilter('album_id',$request["album_id"]);
                if($collection->getSize() > 0) {
                    $collection->walk('delete');
                }
                $model->delete();
                return $resultJson->setData(
                    [
                        'delete' => true,
                    ]
                );
            }catch (\Exception $e) {
                return $resultJson->setData(
                    [
                        'delete' => false,
                    ]
                );
            }
        }
        return $resultJson->setData(
            [
                'delete' => false,
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