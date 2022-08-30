<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Controller\Adminhtml\Font;

use Magento\Framework\App\Filesystem\DirectoryList;
use Designnbuy\Font\Model\Font;
/**
 * Font font save controller
 */
class Save extends \Designnbuy\Font\Controller\Adminhtml\Font
{
    /**
     * Before model save
     * @param  \Designnbuy\Font\Model\Font $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request)
    {
        /* Prepare dates */
        $dateFilter = $this->_objectManager->create('Magento\Framework\Stdlib\DateTime\Filter\Date');
        $data = $model->getData();

        $filterRules = [];


        $inputFilter = new \Zend_Filter_Input(
            $filterRules,
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();
        $model->setData($data);

        /* Prepare author */
        if (!$model->getAuthorId()) {
            $authSession = $this->_objectManager->get('Magento\Backend\Model\Auth\Session');
            $model->setAuthorId($authSession->getUser()->getId());
        }

        /* Prepare relative links */
        $data = $request->getPost('data');
        //$links = isset($data['links']) ? $data['links'] : null;
        $links = isset($data['links']) ? $data['links'] : ['font' => [], 'product' => []];
        
        if ($links && is_array($links)) {
            foreach (['font', 'product'] as $linkType) {
                if (!empty($links[$linkType]) && is_array($links[$linkType])) {
                    $linksData = [];
                    foreach ($links[$linkType] as $item) {
                        $linksData[$item['id']] = [
                            'position' => $item['position']
                        ];
                    }
                    $links[$linkType] = $linksData;
                } else {
                    $links[$linkType] = [];
                }
            }
            $model->setData('links', $links);
        }

        /* Prepare images */
        $data = $model->getData();
        foreach (['woff', 'js','ttf','ttfbold','ttfitalic','ttfbolditalic'] as $key) {
            if (isset($data[$key]) && is_array($data[$key])) {
                if (!empty($data[$key]['delete'])) {
                    $model->setData($key, null);
                } else {
                    if (isset($data[$key][0]['name']) && isset($data[$key][0]['tmp_name'])) {
                        $image = $this->makeSafe($data[$key][0]['name']);
                        //$model->setData($key, Font::BASE_MEDIA_PATH . DIRECTORY_SEPARATOR . $image);
                        $model->setData($key, $image);
                        $imageUploader = $this->_objectManager->get(
                            'Designnbuy\Font\ImageUpload'
                        );
                        $imageUploader->moveFileFromTmp($image);

                    } else {
                        if (isset($data[$key][0]['name'])) {
                            $image = $this->makeSafe($data[$key][0]['name']); 
                            $model->setData($key,$image);
                        }
                    }
                    if(in_array($key,array('ttf','ttfbold','ttfitalic','ttfbolditalic')))
                    {
                        $fontName = $this->createTCPDFont($model,$key);
                        if($fontName!='')
                        $model->setData($key.'_tcpdf', $fontName);
                    }
                }
            } else {
                $model->setData($key, null);
            }
        }
       
        $css = $this->_createCssFromWOFF($model);
        $model->setData('css', $css);
        /* Prepare Media Gallery */
        /*$data = $model->getData();

        if (!empty($data['media_gallery']['images'])) {
            $images = $data['media_gallery']['images'];
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
            $gallery = array();
            foreach ($images as $image) {
                if (empty($image['removed'])) {
                    if (!empty($image['value_id'])) {
                        $gallery[] = $image['value_id'];
                    } else {
                        $imageUploader = $this->_objectManager->get(
                            'Designnbuy\Font\ImageUpload'
                        );
                        $imageUploader->moveFileFromTmp($image['file']);
                        $gallery[] = Font::BASE_MEDIA_PATH . DIRECTORY_SEPARATOR . $image['file'];
                    }
                }
            }

            $model->setGalleryImages($gallery);*/

        }
        private function makeSafe($file)
        {
            // Remove any trailing dots, as those aren't ever valid file names.
            $file = rtrim($file, '.');
        
            $regex = array('#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#');
        
            return trim(preg_replace($regex, '', $file));
        }
        public function createTCPDFont($model,$key){
            $font = $model->getData();
            if($font && $font[$key]){
            $ttfPath = $model->getFontBasePath().DIRECTORY_SEPARATOR.$font[$key];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $directory = $objectManager->get('\Magento\Framework\Filesystem\DirectoryList');
            $rootPath  =  $directory->getRoot();
            $tcpdf_addfont = $rootPath. '/vendor/tecnickcom/tcpdf/tools/tcpdf_addfont.php';
            $tcpdfFontPath = $rootPath.'/vendor/tecnickcom/tcpdf/fonts/';
            return $fontname = $this->addTTFfont($ttfPath, 'TrueTypeUnicode', '', 96);
            //echo shell_exec('php '.$tcpdf_addfont. '  -i  '.$ttfPath. ' 2&>1');exit;
            }
        }
        private function addTTFfont($a,$b,$c,$d)
        {	
            return \TCPDF_FONTS::addTTFfont($a,$b,$c,$d);
        }

        /**
         * @param string $directory
         * @param string $relativeFileName
         * @param string $contents
         * @return void
         */
        protected function _createCssFromWOFF($model) {

            $font = $model->getData();
            $cssName = '';
            if($font && $font['woff']){
                /** @var \Magento\Framework\App\ObjectManager $objectManager */
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                /** @var \Magento\Framework\Filesystem $filesystem */
                $filesystem = $objectManager->get('Magento\Framework\Filesystem');

                /** @var \Magento\Framework\Filesystem\Directory\WriteInterface|\Magento\Framework\Filesystem\Directory\Write $writer */
                $writer = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);

                $cssName = str_replace("woff","css",$font['woff']);
                //$woffName = explode(Font::BASE_MEDIA_PATH. DIRECTORY_SEPARATOR, $font['woff']);
                $woffName = $font['woff'];
                if(isset($woffName) && $woffName != ''){
                    /** @var \Magento\Framework\Filesystem\File\WriteInterface|\Magento\Framework\Filesystem\File\Write $file */
                    //$file = $writer->openFile($cssName, 'w');
                    $file = $writer->openFile(DIRECTORY_SEPARATOR.Font::BASE_MEDIA_PATH . DIRECTORY_SEPARATOR.$cssName, 'w');
                    $woffFile = $model->getWoff();
                    $font_type = " format('woff')";

                    $contents = "@font-face {
                                font-family: '".$model->getTitle()."';
                                src: url('".$woffName."');
                                src: url('".$woffName."')".$font_type .";
                                font-weight: normal;
                                font-style: normal;
                            }";


                    try {
                        $file->lock();
                        try {
                            $file->write($contents);
                        }
                        finally {
                            $file->unlock();
                        }
                    }
                    finally {
                        $file->close();
                    }
                }
                return $cssName;
            }

        }
}
