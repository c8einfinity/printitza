<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\HotFolder\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $outputHelper;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Vendor\Model\User $vendor,
        \Designnbuy\Base\Helper\Output $outputHelper
    ) {
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->outputHelper = $outputHelper;
        $this->vendor = $vendor;
        parent::__construct($context);
    }

    /**
     * Retrieve config value
     * @return string
     */
    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            $param,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isHotFolderEnable()
    {
        return $this->_getConfigValue('hotfolder/configuration/enabled');
    }

    public function isHotFolderEnableForProduct($product)
    {
        if(count($product->getData()) > 0) {
            return $product->getData('is_hotfolder_enable');
        } else {
            return false;
        }
    }

    public function getFolderLocation($product, $isProduct = false)
    {
        if($isProduct){
            return 'product';
        } else if($this->_getConfigValue('hotfolder/configuration/folder_location') == 1) {
            return 'remote';  // Remote Server;
        } else {
            return 'same';  // Same Server
        }
    }

    public function getFolderName($id = 0, $isProduct = false)
    {
        return $this->_getConfigValue('hotfolder/configuration/folder_name');
    }

    public function getSetupFolder()
    {
        return $this->_getConfigValue('hotfolder/configuration/setup');
    }

    public function getFTPCredentials($product = [])
    {
        $ftpCredentials = array();
        $vendorFtp = false;
        if(!empty($product)){
            if($product->getIsHotfolderEnable() == 1 && $product->getFolderLocation() == 2){
                $vendorFtp = true;
            }
        }
        
        $folderLocation = $this->getFolderLocation($product, $vendorFtp);
        //echo $folderLocation;exit;
        
        if($folderLocation == 'product') {
            //echo "<pre>"; print_r(get_class_methods($this->vendor)); exit;
            
            $vendor = $this->vendor->loadByUserId($product->getVendorId());
            //echo "<pre>"; print_r($vendor); exit;
            
            //$vendor = $this->loadVendor($folderLocation);
            $ftpCredentials['ftp_host'] = $vendor->getRemoteHost();
            $ftpCredentials['ftp_port'] = $vendor->getFtpPort();
            $ftpCredentials['ftp_username'] = $vendor->getFtpUsername();
            $ftpCredentials['ftp_password'] = $vendor->getFtpPassword();
            $ftpCredentials['ftp_path'] = $vendor->getFtpPath();
            $ftpCredentials['connection_timeout'] = $vendor->getConnectionTimeout();
            $ftpCredentials['passive_ftp'] = $vendor->getPassiveFtp();
        } else if($folderLocation == 'remote') {
            
            $ftpCredentials['ftp_host'] = $this->_getConfigValue('hotfolder/configuration/remote_host');
            $ftpCredentials['ftp_port'] = $this->_getConfigValue('hotfolder/configuration/ftp_port');
            $ftpCredentials['ftp_username'] = $this->_getConfigValue('hotfolder/configuration/ftp_username');
            $ftpCredentials['ftp_password'] = $this->_getConfigValue('hotfolder/configuration/ftp_password');
            $ftpCredentials['ftp_path'] = $this->_getConfigValue('hotfolder/configuration/ftp_path');
            $ftpCredentials['connection_timeout'] = $this->_getConfigValue('hotfolder/configuration/connection_timeout');
            $ftpCredentials['passive_ftp'] = $this->_getConfigValue('hotfolder/configuration/passive_ftp');
        }
        return $ftpCredentials;
    }



    function cleanString($string) {
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public function outputFolderPrefix($product)
    {
        $cleanPrefix = '';
        $globalPrefix = $this->_getConfigValue('hotfolder/output_configuration/prefix');

        if($product->getPrefix() != '') {
            $cleanPrefix = $this->cleanString($product->getPrefix());
        } else if($globalPrefix != '') {
            $cleanPrefix = $this->cleanString($globalPrefix);
        }
        return $cleanPrefix;
    }

    public function outputFolderPostfix($product)
    {
        $cleanPostfix = '';
        $globalPostfix = $this->_getConfigValue('hotfolder/output_configuration/postfix');

        if($product->getPostfix() != '') {
            $cleanPostfix = $this->cleanString($product->getPostfix());
        } else if($globalPostfix != '') {
            $cleanPostfix = $this->cleanString($globalPostfix);
        }
        return $cleanPostfix;
    }

    public function outputFolderMiddleName($orderId, $itemId, $product)
    {
        $itemOrderNaming = '';
        $globalOrderNaming = $this->_getConfigValue('hotfolder/output_configuration/order_naming');

        if($product->getAttributeText('item_naming') == 'Product ID') {
            //$itemOrderNaming = $product->getId();
            $itemOrderNaming = $product->getId() . '_' .$orderId . '_' . $itemId;
        } else if($globalOrderNaming == 0) { // 0. Order ID and Item ID
            $itemOrderNaming = $orderId . '_' . $itemId;
        }/* else if($globalOrderNaming == 2) {
            $order = Mage::getModel('sales/order')->load($orderId);
            $itemOrderNaming = $order->getCreatedAt();
        }*/
        return $itemOrderNaming;
    }

    public function outputFolderLocation($orderId, $product, $sameServer = true)
    {
        if($product instanceof \Magento\Catalog\Model\Product){
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $enableHotfolderForProduct = $this->isHotFolderEnableForProduct($product);
        if($enableHotfolderForProduct == 1) { // Check hotfolder is enabled for product
            $enableHotfolder = 1;
            $folderName = $this->getFolderName($productId, true); // Get hotfolder path for product
        } else if($enableHotfolderForProduct == false || $enableHotfolderForProduct == 0) {
            $enableHotfolder = $this->isHotFolderEnable();
            $folderName = $this->getFolderName(); // Get global hotfolder path
        }

        //if($folderLocation == 1) { // 1. Same Server
        if($sameServer == true) { // 1. Same Server
            if($folderName != '' && $enableHotfolder == '1') { // If hotfolder enabled
                $mediaPath = $this->outputHelper->getMediaPath();
                $folderPath = $mediaPath . $folderName;
                if(!is_dir($folderPath)) {
                    mkdir($folderPath,0777);
                }

                $setupFolder = $this->getSetupFolder();
                if($setupFolder == 1) {
                    $prefix = 'order';
                    $id = $orderId;
                } else {
                    $prefix = 'product';
                    $id = $productId;
                }

                $destinationFolder = $folderPath  . DIRECTORY_SEPARATOR . $prefix . '_' . $id . DIRECTORY_SEPARATOR;
                if(!is_dir($destinationFolder)) {
                    mkdir($destinationFolder, 0777);
                }
            } else { // If hotfolder disabled, considered default folder media/designnbuy/output
                $mediaPath = $this->outputHelper->getDesignNBuyDir();
                $destinationFolder = $mediaPath . 'output' . DIRECTORY_SEPARATOR;
                if(!is_dir($destinationFolder)) {
                    mkdir($destinationFolder,0777);
                }
            }
        } else {
            if($enableHotfolder == '1') {
                $setupFolder = $this->getSetupFolder();
                if($setupFolder == 1) {
                    $prefix = 'order';
                    $id = $orderId;
                } else {
                    $prefix = 'product';
                    $id = $productId;
                }
                $destinationFolder = $folderName . DIRECTORY_SEPARATOR . $prefix . '_' . $id . DIRECTORY_SEPARATOR;
            } else {
                $mediaPath = $this->outputHelper->getDesignNBuyDir();
                $destinationFolder = 'designnbuy/output' . DIRECTORY_SEPARATOR;
            }
        }
        return $destinationFolder;
    }

    public function remoteFTP($product)
    {
        $ftpCredentials = [];
        $enableHotfolderForProduct = $this->isHotFolderEnableForProduct($product);
        //echo $enableHotfolderForProduct; exit;
        if($enableHotfolderForProduct == 1) {
            $enableHotfolder = 1;
            $ftpCredentials = $this->getFTPCredentials($product);
        } else if($enableHotfolderForProduct == false || $enableHotfolderForProduct == 0) {
            $enableHotfolder = $this->isHotFolderEnable();
            if($enableHotfolder == 1) {
                $ftpCredentials = $this->getFTPCredentials();
            }
        }
        return $ftpCredentials;
    }

}
