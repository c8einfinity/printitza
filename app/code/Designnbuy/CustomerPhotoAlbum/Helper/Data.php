<?php
namespace Designnbuy\CustomerPhotoAlbum\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const OUTPUT_IMAGE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'output'. DIRECTORY_SEPARATOR;

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
     * Album Model
     *
     * @var \Designnbuy\CustomerPhotoAlbum\Model\Photos
     */
    protected $_photos;
    
    /**
     * Media Directory 
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_mediaDirectory;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    protected $_coreSession;

    protected $_layoutFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Designnbuy\CustomerPhotoAlbum\Model\Album $albumModel,
        \Magento\Store\Model\StoreManagerInterface $store_manager,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Album\CollectionFactory $collection,
        \Designnbuy\CustomerPhotoAlbum\Model\ResourceModel\Photos\CollectionFactory $photoscollection,
        \Designnbuy\CustomerPhotoAlbum\Model\Photos $photos,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        $rootDirBasePath = DirectoryList::MEDIA
    ) {
        $this->_collectionFactory = $collection->create();
        $this->_photscollectionFactory = $photoscollection;
        $this->_photos = $photos;
        $this->_albumModel = $albumModel;
        $this->_customerSession = $customerSession;
        $this->_coreSession = $coreSession;
        $this->_fileFactory = $fileFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $this->store_manager=$store_manager;
        parent::__construct($context);
        $this->_isScopePrivate = true;
        $this->_layoutFactory = $layoutFactory;
        $this->rootDirBasePath = $rootDirBasePath;
    }

	public function getCustomerAlbums($id)
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
            )->addFieldToFilter(
                'album_id',
                [ "neq" => $id ]
            )->setOrder('album_id', 'asc');
        }
        return $this->_albums;
    }

    public function getallAlbums()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
       $collection = $this->_collectionFactory->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'customer_id',
            $this->_customerSession->getCustomer()->getId()
        )->setOrder('album_id', 'asc');
        
        return $collection;
    }

    public function getAlbumById($id)
    {
        return $this->_albumModel->load($id);
    }

    public function getAlbumTitle($id)
    {
        return $this->getAlbumById($id)->getTitle();
    }

    public function getPhotosByAlbumId($id)
    {
        $collection = $this->_photscollectionFactory->create();
        $collection->addFieldToFilter('album_id',$id);
        return $collection;
    }
    public function getPhotosById($id)
    {
        $collection = $this->_photscollectionFactory->create();
        $collection->addFieldToSelect('path');
        $collection->addFieldToFilter('photo_id',$id);
        return $collection->getFirstItem()->getData('path');
    }

    public function getImageUrl($image)
    {
        if($image != "" && file_exists($this->getPhotoAlbumImageDir($image))){
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/uploadedImage/'.$image;
        } else {
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/uploadedImage/placeholder.png';
        }
    }

    public function getAdminImageUrl($image)
    {
        if($image != "" && file_exists($this->getPhotoAlbumAdminImageDir($image))){
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/adminimages/'.$image;
        } else {
            return $this->store_manager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'designnbuy/adminimages/placeholder.png';
        }
    }

    public function getOutputDir()
    {
        $directory = $this->_mediaDirectory->getAbsolutePath(self::OUTPUT_IMAGE_PATH);
        return $directory;
    }

    public function getPhotoAlbumImageDir($image)
	{
        return $this->_mediaDirectory->getAbsolutePath('designnbuy/uploadedImage/').$image;
    }

    public function getPhotoAlbumAdminImageDir($image)
	{
        return $this->_mediaDirectory->getAbsolutePath('designnbuy/adminimages/').$image;
    }

	public function getCustomerSession()
    {
        return $this->_customerSession->getCustomer();
    }

    public function getCustomerId(){
        $customer=$this->getCustomerSession();
        return $customer->getId();
    }
    public function checkCustomerLogin(){
        if($this->_customerSession->isLoggedIn()){
            return true;
        } 
        return false;
    }
    public function setProductParams($params)
    {
        $this->_coreSession->start();
        $this->_coreSession->setCustomData($params);
    }
    public function getProductParams()
    {
        $this->_coreSession->start();
        return $this->_coreSession->getCustomData();
    }
    public function unSetProductParams(){
        $this->_coreSession->start();
        return $this->_coreSession->unsCustomData();
    }
    public function getCustomerAlbumsPhotos($id)
    {
        $collection = $this->_photscollectionFactory->create();
        $collection->addFieldToFilter('album_id',$id);
        
        if($collection->getSize() > 0){
            if($collection->getFirstItem()->getPath() != null){
                return $collection->getFirstItem()->getPath();
            }else{
                return "placeholder.png";
            }
        }
        if($collection->getFirstItem()->getPath() != null){
            return $collection->getFirstItem()->getPath();
        }else{
            return "placeholder.png";
        }
    }
    public function getCustomOption($product)
    {
        $layout = $this->_layoutFactory->create();
				
        $blockOptionData = $layout->createBlock("Magento\Catalog\Block\Product\View\Options")->setProduct($product)
                ->setTemplate('Magento_Catalog::product/view/options.phtml');

        $block_links1 = $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\DefaultType', 'default')->setTemplate('Magento_Catalog::product/view/options/type/default.phtml');
        $blockOptionData->setChild('default', $block_links1);

        $block_links2 = $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Text', 'text')->setTemplate('Magento_Catalog::product/view/options/type/text.phtml');
        $blockOptionData->setChild('text', $block_links2);

        $block_links3 = $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\File', 'file')->setTemplate('Magento_Catalog::product/view/options/type/file.phtml');
        $blockOptionData->setChild('file', $block_links3);

        $block_links4 = $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Select', 'select')->setTemplate('Magento_Catalog::product/view/options/type/select.phtml');
        $blockOptionData->setChild('select', $block_links4);

        $block_links5 = $layout->createBlock('Magento\Catalog\Block\Product\View\Options\Type\Date', 'date')->setTemplate('Magento_Catalog::product/view/options/type/date.phtml');
        $blockOptionData->setChild('date', $block_links5);

        $option_price_renderer_block = $layout
        ->createBlock(
                "Magento\Framework\Pricing\Render", "product.price.render.default", [
            'data' => [
                'price_render_handle' => 'catalog_product_prices',
                'use_link_for_as_low_as' => 'true'
            ]
                ]
        )
        ->setData('area', 'frontend');

        $blockOptionData->setChild('product.price.render.default', $option_price_renderer_block);
        $blockOptionData->setProduct($product);
        
        if (!empty($blockOptionData->toHtml()) && strlen($blockOptionData->toHtml()) > 1) {
            return $blockOptionData->toHtml();
        } else {
            return false;
        }

    }
    public function zipPhotoAlbum($photo_ids,$folder_location,$filename){
        $photos = json_decode($photo_ids,true);
        try {
            if(!empty($photos)){
                if($filename != ""){
                    $outputPath = $this->getOutputDir() . $filename . DIRECTORY_SEPARATOR;
                    
                    foreach ($photos as $photo) {
                        if($this->getPhotosById($photo)){
                            if(file_exists($this->getPhotoAlbumImageDir($this->getPhotosById($photo)))){
                                if (!is_dir($outputPath)) {
                                    mkdir($outputPath, 0777);
                                }
                                copy($this->getPhotoAlbumImageDir($this->getPhotosById($photo)), $outputPath.$this->getPhotosById($photo));
                            }

                        }
                    }
                        $zip = new \ZipArchive();
                        if ($zip->open($this->getOutputDir() . $filename . '.zip', \ZIPARCHIVE::CREATE) === true) {
                            foreach (glob($outputPath . '/*') as $file) {
                            
                                $zip->addFile($file, substr($file, strlen($outputPath)));

                            }
                        }
                        $zip->close();
                        
                        $this->deleteDir($outputPath);
                        
                        $this->_fileFactory->create(
                            $filename . '.zip',
                            ['value' => $this->getOutputDir().$filename . '.zip', 'type' => 'filename'],
                            $this->rootDirBasePath
                        );

                        return true;
                } else {
                    throw new InvalidArgumentException("Something went wrong while downloading output or output files are missing !!!");
                }
            } else {
                throw new InvalidArgumentException("Something went wrong while downloading output or output files are missing !!!");
            }
        } catch (\Exception $e) {
            echo $e->getMessage(); exit;
        }
    }
    public static function deleteDir($dirPath) {
        if (! is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }
    public function saveAdminImage($imageName,$isHD=null,$imageId=null,$albumId,$files){
        
        if(isset($albumId) && !empty($imageName)) {
            
            if($albumId == ""){
                $defaultAlbum = "Default";
                $album = $this->_albumModel->getCollection();
                $album->addFieldToFilter('customer_id',"999999999");
                $album->addFieldToFilter('title',$defaultAlbum);
                if (empty($album->getData())) {
                    $albumModel = $this->_albumModel;
                    $albumModel->setTitle($defaultAlbum);
                    $albumModel->setCustomerId("999999999");
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
            if($imageId){
                $photosModel->load($imageId);
            }
            
            $photosModel->setAlbumId($albumId);
            if(!empty($files)){
            $photosModel->setPath($imageName);
            }
            $photosModel->save();
            return $photosModel->getPhotoId();
        }
    }
}
