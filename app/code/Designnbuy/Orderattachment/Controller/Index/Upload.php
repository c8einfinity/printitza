<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Orderattachment\Controller\Index;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;

class Upload extends \Magento\Framework\App\Action\Action
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
            /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
            $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                ->getDirectoryRead(DirectoryList::MEDIA);
                
            $allowExtension = $this->_helper->getAllowExtensionValue();
           
            $path = $this->_helper->getFilePath();
            $urlPath = \Designnbuy\Orderattachment\Helper\Data::FILE_PATH;
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => 'attachment']
            );
            $allowExtensionList = explode(' ', $allowExtension);
            $uploader->setAllowedExtensions($allowExtensionList);

            $mediaPath = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')
                ->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

            $attachmentPath = \Designnbuy\Orderattachment\Helper\Data::FILE_PATH;

            $mediaAttachmentUrl = $mediaPath . $attachmentPath;

            $fileDir = $this->_helper->getFilePath();
            if (!file_exists($fileDir)) {
                mkdir($fileDir, 0777, true);           
            }
            
            $files = $this->getRequest()->getFiles();
            $fileNname = str_replace(" ","_",$files['attachment']['name']); 
            $exploded_fileNname = explode(".",$fileNname);

            /*$fileNameOnly = $exploded_fileNname[0];
            $fileType = $exploded_fileNname[1];

            if (strpos($allowExtension, $fileType) !== false) {
            }else {                
                $msg = "File type not allowed, Upload valid file";
                throw new \Exception($msg);
            }*/

            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(false);
            
            if($result = $uploader->save($path)) {
                $pathInfo = pathinfo($result['path'].$result['file']);
                $fileType = $pathInfo['extension'];

                switch (strtolower($fileType))
                {
                    case 'svg':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/svg.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = '/icons/svg.svg';
                        break;

                    case 'cdr':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/cdr.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = '/icons/cdr.svg';
                        break;

                    case 'doc':
                    case 'docx':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/doc.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $iconName = '/icons/doc.svg';
                        break;
                    
                    case 'csv':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/csv.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = '/icons/csv.svg';
                        break;

                    case 'ps':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/ps.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = '/icons/ps.svg';
                        break;

                    case 'swf':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/swf.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = '/icons/swf.svg';
                        break;

                    case 'xls':
                    case 'xlsx':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/xls.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $iconName = "icons/xls.svg";
                        break;
                    
                    case 'tgz':
                    case 'zip':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/zip.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = "icons/zip.svg";
                        break;

                    case 'rar':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/rar.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $iconName = "icons/rar.svg";
                        break;

                    case '7z':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/7z.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file'];
                        $finalName = $result['file'];
                        $iconName = "icons/7z.svg";
                        break;

                    case 'txt':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/txt.png';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $iconName = "icons/txt.svg";
                        break;

                    case 'pdfA':
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/pdf.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $iconName = '/icons/pdf.svg';
                        break;
                    
                    case 'jpg':
                    case 'png':
                    case 'jpeg':
                    case 'gif':
                    case 'ico':
                    case 'btm':
                        $imageUrlPath = $mediaPath.$attachmentPath.$result['file']; 
                        $finalName = $result['file'];
                        $result['file_url'] = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $urlPath . $result['file'];
                    break;

                    case 'tiff':
                    case 'tif':
                        $file_dir_path = $fileDir.$fileNname;
                        $file_url_path = $mediaPath.$attachmentPath.$result['file'];
                        $image_dir_path = $fileDir.$result['file'].".png";
                        exec("convert $file_dir_path -density 72 -quality 80 $image_dir_path");
                        $imageUrlPath = $mediaPath.$attachmentPath.$result['file'].".png";
                        $finalName = $result['file'];
                        $iconName = $result['file'].".png";
                        $result['file_url'] = $file_url_path;
                        break;

                    case 'ai':
                    case 'psd':
                        $file_dir_path = $fileDir.$fileNname."[0]";
                        $file_url_path = $mediaPath.$attachmentPath.$result['file'];
                        //$image_dir_path = $fileDir.$fileNameOnly."-0.png";
                        $image_dir_path = $fileDir.$result['file'].".png";
                        exec("convert -density 400 $file_dir_path -quality 100 $image_dir_path");
                        $imageUrlPath = $mediaPath.$attachmentPath.$result['file'].".png";
                        $finalName = $result['file'] . ".png";
                        $result['file_url'] = $file_url_path;
                        $iconName = $result['file'].".png";
                    break;

                    case 'pdf':
                        $file_dir_path = $fileDir.$fileNname;
                        if($fileType == "pdf" ) {
                            $file_dir_path = $fileDir.$fileNname."[0]";    
                        }
                        $file_url_path = $mediaPath.$attachmentPath.$result['file'];
                        $image_dir_path = $fileDir.$result['file'].".png";
                        exec("convert -density 400 $file_dir_path -quality 100 $image_dir_path");  
                        $imageUrlPath = $mediaPath.$attachmentPath.$result['file'].".png";
                        $finalName = $result['file'];
                        $iconName = $result['file'].".png";
                        $result['file_url'] = $file_url_path;
                    break;

                    case 'eps':
                        $file_dir_path = $fileDir.$fileNname;
                        $file_url_path = $mediaPath.$attachmentPath.$result['file'];
                        $image_dir_path = $fileDir.$result['file'].".png";
                        exec("convert -density 300 colorspace sRGB $file_dir_path PNG32:$image_dir_path");//to make background transparent
                        $imageUrlPath = $mediaPath.$attachmentPath.$result['file'].".png";
                        $finalName = $result['file'];
                        $iconName = $result['file'].".png";
                        $result['file_url'] = $file_url_path;
                        break;
                    
                    default:
                        $imageUrlPath = $mediaAttachmentUrl.'/icons/unknown.svg';
                        $result['file_url'] = $mediaPath.$attachmentPath.$fileNname; 
                        $finalName = $fileNname;
                        $iconName = '/icons/unknown.svg';
                    break;
                }
                $result['success'] = true;
                $result['finalName'] = $finalName;

                $iconName = (isset($iconName)) ? $iconName : $finalName;
                $result['iconName'] = $iconName;
                $result['preview_url'] = $imageUrlPath;
                $result['file_path'] = $fileDir.$finalName;
                $result['dir'] = $fileDir;
                $result['allow_extension'] = $allowExtension;
            }
            unset($result['tmp_name']);
            unset($result['path']);
            //$result['attachment_url'] = $this->_urlBuilder->getBaseUrl(['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]) . $urlPath . $result['file'];
            //$result['file'] = $result['file'];
        } catch (\Exception $e) {
            //echo "<pre>"; print_r($e->getMessage()); exit;
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
