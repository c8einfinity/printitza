<?php
namespace Designnbuy\CustomerPhotoAlbum\Plugin;
 
class CustomerImage
{   
    protected $_mediaDirectory;

    protected $_album;

    protected $_customerImageFactory;

    protected $_photos;

    protected $dnbHelper;

    public function __construct(
        \Designnbuy\CustomerPhotoAlbum\Model\Album $album,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\Customer\Model\ImageFactory $customerImageFactory,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Designnbuy\Base\Helper\Data $dnbHelper
    ) {
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->_album = $album;
        $this->_customerImageFactory = $customerImageFactory;
        $this->_photos = $photos;
        $this->dnbHelper = $dnbHelper;
    }
    public function afterSaveUserImage(\Designnbuy\Customer\Helper\Data $subject, $result, $imageName, $isHD=null, $imageId=null)
    {            
        $image = $this->_customerImageFactory->create();
        $image->load($result);
        
        if(isset($_REQUEST['cur_album_id']) && !empty($image->getData()) ) {
            
            $albumId = $_REQUEST['cur_album_id'];
            
            if($albumId == ""){
                $defaultAlbum = "Default";
                $album = $this->_album->getCollection();
                $album->addFieldToFilter('customer_id',$image->getCustomerId());
                $album->addFieldToFilter('title',$defaultAlbum);
                if (empty($album->getData())) {
                    $albumModel = $this->_album;
                    $albumModel->setTitle($defaultAlbum);
                    $albumModel->setCustomerId($image->getCustomerId());
                    $albumModel->save();
                    $albumId = $albumModel->getAlbumId();
                } else {
                    $albumId = $album->getData()[0]['album_id'];
                }
            }
            /* $sourceDir = $this->dnbHelper->getCustomerImageDir();
            if (!file_exists($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'))) {
                mkdir($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/'), 0777, true);
            }
            file_put_contents($this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/').$image->getImage(), file_get_contents($sourceDir.$imageName)); */
            $photosModel = clone $this->_photos;
            $photosModel->setAlbumId($albumId);
            $photosModel->setPath($image->getImage());
            $photosModel->save();
            return $photosModel->getPhotoId();
        }
        
        return $result;
    }
}
?>