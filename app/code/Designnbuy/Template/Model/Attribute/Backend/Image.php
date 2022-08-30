<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Template\Model\Attribute\Backend;
use Magento\Framework\App\Filesystem\DirectoryList;
/**
 * Backend model for attribute with file
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Image extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * File Uploader factory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploaderFactory;
    /**
     * Filesystem facade
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    private $imageUploader;
    protected $_logger;
    /**
     * @param
     * @codeCoverageIgnore
     */
    public function __construct(
        \Designnbuy\Template\Helper\Template $templateHelper,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_logger                      = $logger;
    }

    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $values = $object->getData($attrCode);
        if (!$object->hasData($attrCode)) {
            $object->setData($attrCode, NULL);
        } else {
            $values = $object->getData($attrCode);
            if (is_array($values)) {
                $object->setData($this->getAttribute()->getAttributeCode(), $this->_getImageFields($values));
            }
        }
        return parent::beforeSave($object);
    }

    protected function _getImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data) && isset($data)) {
                return $imageName = $data[0]['file'];
            }
        }
        return $imageName;
    }
    
    /**
     * Save uploaded file and set its name to file object
     *
     * @access public
     * @param Varien_Object $object
     * @return null
     * @author Ultimate Module Creator
     */
    public function afterSave($object)
    {
        $value = $object->getData($this->getAttribute()->getName());

        if (is_array($value) && !empty($value['delete'])) {
            $object->setData($this->getAttribute()->getName(), '');
            $this->getAttribute()->getEntity()
                ->saveAttribute($object, $this->getAttribute()->getName());
            return;
        }
        $image = $object->getData($this->getAttribute()->getName(), null);

        if ($image !== null) {
            try {
                //if ($this->isTmpFileAvailable($image) && $imageName = $this->getUploadedImageName($value)) {
                    $fileName = $this->getImageUploader()->moveFileFromTmp($image);
                    if ($fileName) {
                        $object->setData($this->getAttribute()->getName(), $fileName);
                        $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
                    }

                    $object->setData($this->getAttribute()->getName(), $fileName);
                    $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
                //}
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }

        try {
            /*$path = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)
                ->getAbsolutePath(\Designnbuy\Template\Helper\Template::BASE_MEDIA_PATH);

            $uploader = $this->_fileUploaderFactory->create(['fileId' => "template[".$this->getAttribute()->getName()."]"]);
            $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            $result = $uploader->save($path);

            $fileName = $uploader->getUploadedFileName();
            if ($fileName) {
                $object->setData($this->getAttribute()->getName(), $fileName);
                $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());
            }

            $object->setData($this->getAttribute()->getName(), $result['file']);
            $this->getAttribute()->getEntity()->saveAttribute($object, $this->getAttribute()->getName());*/

        } catch (\Exception $e) {
            return $this;
        }
    }

    private function getImageUploader()
    {
        if ($this->imageUploader === NULL) {
            $this->imageUploader = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Magento\Catalog\CategoryImageUpload'
            );
            $this->imageUploader->setBaseTmpPath('tmp/catalog/product');
            $this->imageUploader->setBasePath('designnbuy/template');
        }
        return $this->imageUploader;
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
}
