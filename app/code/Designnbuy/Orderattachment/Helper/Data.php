<?php
/**
* Copyright © 2018 Porto. All rights reserved.
*/

namespace Designnbuy\Orderattachment\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const FILE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'orderattachment'. DIRECTORY_SEPARATOR;

    protected $_objectManager;

    protected $_mediaDirectory;

    protected $_scopeConfig;
    
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Filesystem $filesystem
    ) {        
        $this->_objectManager= $objectManager;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        parent::__construct($context);
    }

    public function getCartDesignsUrl()
    {
        return $this->getMediaUrl().self::FILE_PATH;
    }

    public function getFilePath()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::FILE_PATH);
    }

    public function getImageUrlPath()
    {
        $mediaPath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). self::FILE_PATH;
        return $mediaPath;
    }

    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue($param, ScopeInterface::SCOPE_STORE);
    }

    public function getIsEnabled()
    {
        return $this->_getConfigValue('orderattachment/attachmentgroup/enabled');
    }

    public function getAllowExtensionValue() {
        return $this->_getConfigValue('orderattachment/attachmentgroup/file_extension');
    }

    public function getMaxFileSize()
    {
        return $this->_getConfigValue('orderattachment/attachmentgroup/max_file_size');
    }

    public function getAttachments($_item)
    {
        $productOptions = $_item->getBuyRequest()->getData();
        $itemAttachments = [];
        if( array_key_exists('attachment',$productOptions) && isset($productOptions['attachment'])){
            $itemAttachments = $productOptions['attachment'];
        }
        return $itemAttachments;
    }
}
