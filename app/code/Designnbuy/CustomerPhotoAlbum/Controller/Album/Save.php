<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Album;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResourceConnection;


class Save extends \Magento\Framework\App\Action\Action
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
	
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $albumFactory,
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
        $this->resource = $resource;
        return parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest()->getParams();
        if(isset($request['uploaded_files']) && $request['uploaded_files'] != ""){
            $myFiles = explode("|",$request['uploaded_files']);
        }
        
        $bgImage = $this->getRequest()->getFiles('albumfiles');
        
        $albumId = '';
        if(isset($request["album_name"]) && $request["album_name"] !=''){
            $model = $this->_album;
            $model->setTitle($request["album_name"]);
            $model->setCustomerId($this->getCustomerId());
            $model->save();
            $albumId = $model->getAlbumId(); 
        }
        if(isset($request["move_album"]) && $request["move_album"] != ""){
            $albumId = $request["move_album"]; 
        }
        
        if (!empty($myFiles)) {
            
            try{
                for($i=0;$i<count($myFiles);$i++){
                    if(isset($request['removed_files']) && $request['removed_files'] != ""){
                        $removedFiles = explode("|",$request['removed_files']);
                        if (in_array($myFiles[$i], $removedFiles))
                        {
                            continue;
                        }
                    }
                    $target = $this->_mediaDirectory->getAbsolutePath('designnbuy/temp/');
                    
                    if ($myFiles[$i])
                    {
                        $target_dir = $target.$myFiles[$i];
                        
                        $ext = pathinfo($myFiles[$i],PATHINFO_EXTENSION);
                        $filename = 'image_'.date("YmdHis").'_'.$i.'.'.$ext;
                        $target_save = $this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/').$filename;
                        rename($target_dir,$target_save);
                        $photosModel = clone $this->_photos;
                        $photosModel->setAlbumId($albumId);
                        $photosModel->setPath($filename);
                        try{
                            $photosModel->save();
                        } catch (\Exception $e) {
                            $this->messageManager->addError($e->getMessage());
                            return $this->resultRedirectFactory->create()->setPath('photoalbum/album/create/', ['_current' => true]);
                        }
                    }
                }
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        } else {
            if(!isset($request["move_album"])){
                $this->messageManager->addError("Please upload photos for photo album");
                return $this->resultRedirectFactory->create()->setPath('photoalbum/album/create/', ['_current' => true]);
            }
        }
            return $this->resultRedirectFactory->create()->setPath('photoalbum/album/edit/', ['_current' => true,'id' => $albumId]);
    }

	public function convertToReadableSize($size)
	{
	  $base = log($size) / log(1024);
	  $suffix = array("", "KB", "MB", "GB", "TB");
	  $f_base = floor($base);
	  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
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