<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Orderattachment\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class DropboxUpload extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Designnbuy\Merchandise\Helper\Data
     */
    protected $_helper;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Designnbuy\Orderattachment\Helper\Data $helper,
        UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $url = $this->getRequest()->getParam('url');
            if($url){
                $allowExtension = $this->_helper->getAllowExtensionValue();
                if($allowExtension){
                    $allowExtension = explode(" ",$allowExtension);
                }
                
                $path = $this->_helper->getFilePath();
                
                $mediaPath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                    ->getStore()
                    ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

                $attachmentPath = \Designnbuy\Orderattachment\Helper\Data::FILE_PATH;
                
                $mediaAttachmentUrl = $mediaPath . $attachmentPath;
                
                $url = urldecode($url);
                $url = str_replace(' ', '+', $url);
                $imageData = explode('.',$url);
                $dotCount = count($imageData);
                $imageName = uniqid().'.'.$imageData[$dotCount-1];
                
                //$imageName = 'designnbuy/uploadedImage/'.$name;
                $saveto =  $path;
                
                $ch = curl_init ($url);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
                $raw=curl_exec($ch);

                curl_close ($ch);
                
                if(file_exists($saveto . $imageName)){
                    unlink($saveto . $imageName);
                }
                //$fp = fopen($saveto);
                $fp = fopen($saveto . $imageName, 'w');
                fwrite($fp, $raw);
                fclose($fp);
                
                $result = array();
                $result['success'] = true;
                $result['imageName'] = $imageName;
                $result['imageUrl'] = $mediaAttachmentUrl.$imageName;
                
            } else {
                $result = ['error' => 'Not geting image from dropbox'];
            }
            
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
