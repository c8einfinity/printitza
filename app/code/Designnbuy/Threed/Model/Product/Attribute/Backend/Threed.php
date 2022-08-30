<?php
namespace Designnbuy\Threed\Model\Product\Attribute\Backend;

class Threed extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    protected $_uploaderFactory;
    protected $_filesystem;
    protected $_fileUploaderFactory;
    protected $_logger;
    private $imageUploader;
    /**
     * @var \Designnbuy\Threed\Helper\Data
     */
    protected $_helper;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var string
     */
    private $additionalData = '_additional_data_';
    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $mediaDirectory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Designnbuy\Threed\Helper\Data $helper,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->_filesystem                = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_logger                      = $logger;
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
    }

    private function getImageUploader()
    {
        if ($this->imageUploader === NULL) {
            $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Catalog\CategoryImageUpload'
            );
            $this->imageUploader->setBaseTmpPath('tmp/catalog/product');
            $this->imageUploader->setBasePath('catalog/product');
        }
        return $this->imageUploader;
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value['file'][0]['file'])) {
            return $value['file'][0]['file'];
        }

        return '';
    }

    /**
     * Avoiding saving potential upload data to DB
     * Will set empty image attribute value if image was not uploaded
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @since 101.0.8
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if ($imageName = $this->getUploadedImageName($value)) {
            $object->setData($this->additionalData . $attrCode, $value);
            $object->setData($attrCode, $imageName);
        } elseif (is_string($value)) {
            $object->setData($attrCode, '');
        }


        return parent::beforeSave($object);
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value['file'][0]['status']) && $value['file'][0]['status'] == 'new';
    }

    /**
     * Save uploaded file and set its name to category
     *
     * @param \Magento\Framework\DataObject $object
     * @return \Magento\Catalog\Model\Category\Attribute\Backend\Image
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->additionalData . $this->getAttribute()->getName());

        if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
            try {
                $this->getImageUploader()->moveFileFromTmp($imageName);
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
        return $this;
    }


    protected function _getImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data['file']) && isset($data['file'])) {
                return $imageName = $data['file'][0]['file'];
            }
        }
        return $imageName;
    }
}