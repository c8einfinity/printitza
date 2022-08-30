<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Merchandise\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Base media folder path
     */
    const IMAGE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'side'. DIRECTORY_SEPARATOR .'image';
    const COLOR_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'side'. DIRECTORY_SEPARATOR .'color';
    const MASK_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'side'. DIRECTORY_SEPARATOR .'mask';
    const OVERLAY_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'side'. DIRECTORY_SEPARATOR .'overlay';
    const DESIGN = 'design';
    const TEMPLATE = 'template';

    /**#@+
     * Product Sides values
     */
    const SIDES = 4;
    const SIDE_0 = 0;
    const SIDE_1 = 1;
    const SIDE_2 = 2;
    const SIDE_3 = 3;

    const COLOR_FIELD = 'color';
    const SIZE_FIELD = 'size';

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute
     */
    protected $_eavAttribute;
    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    protected $productRepository;
    
    protected $designideaCollectionFactory;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory $designideaCollectionFactory
    ) {
        $this->_eavAttribute = $eavAttribute;
        $this->_imageFactory = $imageFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        $this->_moduleManager = $moduleManager;
        $this->_url = $url;
        $this->productRepository = $productRepository;
        $this->designideaCollectionFactory = $designideaCollectionFactory;
        parent::__construct($context);
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::SIDE_0 => 'Front',
            self::SIDE_1 => 'Back',
            self::SIDE_2 => 'Left',
            self::SIDE_3 => 'Right'
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getSideOptions()
    {
        $result = [];
        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value, 'color' => '', 'image' => '', 'mask' => '', 'overlay' => ''];
        }
        return $result;
    }
    /**
     * Return full path to file
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    public function getFilePath($path, $file)
    {
        $path = rtrim($path, '/');
        $file = ltrim($file, '/');

        return $path . '/' . $file;
    }

    /**
     * Return file name form file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = substr($pathFile, strrpos($pathFile, '/') + 1);

        return $file;
    }

    /**
     * Get filesize in bytes.
     * @param string $file
     * @return int
     */
    public function getFileSize($file)
    {
        return $this->_mediaDirectory->stat($file)['size'];
    }

    public function fileExists($filename)
    {
        return $this->_mediaDirectory->isFile($filename);
    }

    public function getImagePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::IMAGE_PATH);
    }

    public function getColorImagePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::COLOR_PATH);
    }

    public function getMaskImagePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::MASK_PATH);
    }

    public function getOverlayImagePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::OVERLAY_PATH);
    }

    public function getStartFromScratchPageUrl($_product)
    {
        $params = [];
        $params['id'] =   $_product->getId();
        return $this->_getUrl('merchandise/index/index', $params);
    }

    public function getPersonalisePageUrl($_product)
    {
        $params = [];
        if($_product->getMerchandisePersonalizeOption() == 1 || $_product->getMerchandisePersonalizeOption() == 2){
            if($_product->getDesignideaId()){
                $params['designidea_id'] =   $_product->getDesignideaId();
                $params['id'] =   $_product->getId();
                if($_product->getDesignideaId()){
                    $designIdea = $this->designideaCollectionFactory->create()
                                ->addAttributeToSelect('identifier')
                                ->getItemById($_product->getDesignideaId());
                    if(!empty($designIdea->getData())){
                        if($designIdea->getIdentifier() != "" && $_product->getUrlKey() != ""){
                            $designIdea_product_url = trim($_product->getUrlKey(),'/').'/'.trim($designIdea->getIdentifier(),'/').'.html';
                            
                            return rtrim($this->_getUrl($designIdea_product_url),'/');
                        }
                    }
                }
            } else {
                $params['id'] =   $_product->getId();
            }

        } else {
            $params['id'] =   $_product->getId();
        }
        return $this->_getUrl('merchandise/index/index', $params);
    }

    public function getDesignIdeaPersonalisePageUrl($designIdea)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $merchandiseModel = $objectManager->get('Designnbuy\Merchandise\Model\Merchandise');
        if($designIdea->getProductId()) {
            $productId = $designIdea->getProductId();
            if($productId == ''){
                $productId = $merchandiseModel->getDefaultProduct();
            }

            try {
                $product = $this->productRepository->getById($productId);
            }  catch (\Magento\Framework\Exception\NoSuchEntityException $e){
                $product = false;
            }

            if(!$product) {
                $productId = $merchandiseModel->getDefaultProduct();
            }

        } else {
            $productId = $merchandiseModel->getDefaultProduct();
        }

        return $this->_getUrl('merchandise/index/index', array(
            'id' => $productId,
            'designidea_id' => $designIdea->getId()
        ));
    }

    public function getTemplatePersonalisePageUrl($designIdea)
    {
        if($designIdea->getProductId()){
            $productId = $designIdea->getProductId();
        } else {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $merchandiseModel = $objectManager->get('Designnbuy\Merchandise\Model\Merchandise');
            $productId = $merchandiseModel->getDefaultProduct();
        }
        return $this->_getUrl('merchandise/index/index', array(
            'id' => $productId,
            'template_id' => $designIdea->getId()
        ));
    }

    public function getColorAttributeId()
    {
        return $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', self::COLOR_FIELD);
    }

    public function getSizeAttributeId()
    {
        return $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', self::SIZE_FIELD);
    }

    public function getDesignUrl($identifier)
    {
        return $this->_url->getUrl(self::DESIGN . '/' . $identifier);
    }

    public function getTemplateUrl($identifier)
    {
        return $this->_url->getUrl(self::TEMPLATE . '/' . $identifier);
    }
    
    public function isModuleEnabled($moduleName)
    {
        return $this->_moduleManager->isEnabled($moduleName);
    }
}
