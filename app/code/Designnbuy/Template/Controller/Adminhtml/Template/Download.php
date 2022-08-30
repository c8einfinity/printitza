<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

use DOMDocument;

/**
 * Edit Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Download extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * @var StoreFactory
     */
    protected $storeFactory;

    
    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        $id = $this->getRequest()->getParam('id');
        $template = $this->_templateFactory->create();

        if ($id) {
            $template->load($id);
            
            $savedSvg = explode(",", $template->getSvg());
            $svgImagePath = $this->dnbBaseHelper->getTemplateSVGDir();
            foreach($savedSvg as $svg){
                if(file_exists($svgImagePath) && $svg!=''){
                    if(file_exists($svgImagePath.$svg)){
                        if(!file_exists($svgImagePath.'template_'.$id)){
                            mkdir($svgImagePath.'template_'.$id, 0777);
                        }
                        copy($svgImagePath.$svg,$svgImagePath.'template_'.$id.'/'.$svg);
                    }
                    $svgfileContents = file_get_contents($svgImagePath.$svg);
                    $doc = new DOMDocument();
                    $doc->preserveWhiteSpace = False;
                    $doc->loadXML($svgfileContents);
                    foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element):
                        foreach ($element->getElementsByTagName("*") as $tags):
                            if($tags->localName=='image' && $tags->getAttribute('xlink:href')!=''):
                                $imageUrl = $tags->getAttribute('xlink:href');
                                $uploadedImage = explode('media/',$imageUrl);
                                if($uploadedImage[0]!='' && file_exists($this->dnbBaseHelper->getMediaPath().$uploadedImage[1])):
                                    $imgName = pathinfo($this->dnbBaseHelper->getMediaPath().$uploadedImage[1],PATHINFO_BASENAME);
                                    copy($this->dnbBaseHelper->getMediaPath().$uploadedImage[1],$svgImagePath.'template_'.$id.'/'.$imgName);
                                endif;
                            endif;
                        endforeach;
                    endforeach;
                    $pageDataAry[] = $doc->saveXML();
                    //$pageDataAry[] = file_get_contents($svgImagePath);
                }
            }
            //echo 'here'; exit;
            $outputPath = $svgImagePath.'template_'.$id.'/';
            $destination = $svgImagePath.'template_'.$id.'.zip';
            $zip = new \ZipArchive();
            if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
                foreach (glob($outputPath . '/*') as $file) {
                    if ($file !== $destination) {
                        $zip->addFile($file, substr($file, strlen($outputPath)));
                    }
                }
                $zip->close();
            }
            $this->deleteDir($outputPath);
            
            if (!$template->getId()) {
                $this->messageManager->addError(__('This template no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            } else {
                $info['title'] = 'template_'.$id.'.zip';
                $info['path'] = $svgImagePath.$info['title'];
                
                $this->_fileFactory->create(
                    $info['title'],
                    ['value' => $info['path'], 'type' => 'filename'],
                    $this->rootDirBasePath
                );
                //$this->_outputHelper->downloadFile($info);
                /* $this->_fileFactory->create(
                    $info['title'],
                    ['value' => $info['path'], 'type' => 'filename'],
                    \Magento\Framework\App\Filesystem\DirectoryList::ROOT,
                    'application/zip'
                ); */
            }
        } else {
            $this->messageManager->addError(__('This template no longer exists.'));
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setPath('*/*/');
        }
    
        return $resultPage;
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
}
