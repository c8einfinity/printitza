<?php
namespace Designnbuy\CustomerPhotoAlbum\Controller\Adminhtml\album;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{

    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    protected $_album;
    protected $_albumFactory;

    protected $date;
    protected $timezone;
    protected $_photos;
	
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $albumFactory,
		\Magento\Framework\Stdlib\DateTime\DateTime $date,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
    )
    {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->date = $date;
        $this->timezone = $timezone;
        $this->_albumFactory = $albumFactory;
        $this->_album = $album;
        $this->_photos = $photos;
        return parent::__construct($context);
    }

    public function execute()
    {
        $request = $this->getRequest()->getParams();
        
        $bgImage = $this->getRequest()->getFiles('albumfiles');
        $albumId = '';
        if(isset($request["title"]) && $request["title"] !=''){
            if(isset($request['album_id']) && $request['album_id'] != "") {
                $model = $this->_album->load($request['album_id']);
            } else {
                $model = $this->_album;
            }
            $model->setTitle($request["title"]);
            $model->setCustomerId($this->getCustomerId());
            $model->setStores($request["stores"]);
            $model->setStatus($request["status"]);
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Album has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Album.'));
            }

            //$this->_getSession()->setFormData($data);
            //return $resultRedirect->setPath('*/*/edit', ['album_id' => $this->getRequest()->getParam('album_id')]);

            $albumId = $model->getAlbumId(); 
        }
        if(isset($request["move_album"]) && $request["move_album"] != ""){
            $albumId = $request["move_album"]; 
        }
        
        if (isset($_FILES['albumfiles']['name'][0]) && !empty($_FILES['albumfiles']['name'][0])) {
            if (!file_exists($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'))) {
                mkdir($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'), 0777, true);           
            }
            if (!file_exists($this->_mediaDirectory->getAbsolutePath('designnbuy/temp/'))) {
                mkdir($this->_mediaDirectory->getAbsolutePath('designnbuy/temp/'), 0777, true);           
            }
            try{
                for($i=0;$i<count($bgImage);$i++){
                    
                    $target = $this->_mediaDirectory->getAbsolutePath('designnbuy/temp/');
                    $mediaPath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                        ->getStore()
                        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
                        
                            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'albumfiles['.$i.']']);
                            $uploader->setAllowedExtensions(['jpg','png','jpeg']);
                            $uploader->setAllowRenameFiles(true);
                            $result = $uploader->save($target);
                        
                    
                    $bgImage[$i]['name'] = str_replace(' ', '_', $bgImage[$i]['name']);
                    $target_dir = $target.$bgImage[$i]['name'];
                    if ($result['file'])
                    {
                        $ext = pathinfo($bgImage[$i]['name'],PATHINFO_EXTENSION);
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
        } /* else {
            if(!isset($request["move_album"])){
                $this->messageManager->addError("Please select photos for photo album");
                return $this->resultRedirectFactory->create()->setPath('photoalbum/album/create/', ['_current' => true]);
            }
        } */
        if ($this->getRequest()->getParam('back')) {
            return $this->resultRedirectFactory->create()->setPath('*/*/edit', ['album_id' => $albumId, '_current' => true]);
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/');

        //    return $this->resultRedirectFactory->create()->setPath('photoalbum/album/edit/', ['_current' => true,'id' => $albumId]);
    }

	public function convertToReadableSize($size)
	{
	  $base = log($size) / log(1024);
	  $suffix = array("", "KB", "MB", "GB", "TB");
	  $f_base = floor($base);
	  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
	}
	
    public function getCustomerId(){
        return 999999999;
    }
}