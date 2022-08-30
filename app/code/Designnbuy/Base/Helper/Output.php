<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Base\Helper;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use DOMDocument;
//use INKSCAPE_INKSCAPE;
use Designnbuy\Base\Service\Inkscape as Inkscape;
/**
 * Designnbuy Output Helper
 */
class Output extends \Magento\Framework\App\Helper\AbstractHelper
{

    const DESIGNNBUY_PATH = 'designnbuy'. DIRECTORY_SEPARATOR ;

    const CART_IMAGE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'cart'. DIRECTORY_SEPARATOR;

    const OUTPUT_IMAGE_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'output'. DIRECTORY_SEPARATOR;

    const TEMPLATE_DESIGN_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'template'. DIRECTORY_SEPARATOR;

    const DESIGNIDEA_DESIGN_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'designidea'. DIRECTORY_SEPARATOR;

    const DNB_TEMP_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'temp'. DIRECTORY_SEPARATOR;

    const CUSTOMER_SAVED_DESIGNS_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'designs'. DIRECTORY_SEPARATOR;

    const JOB_DESIGNS_PATH = 'designnbuy'. DIRECTORY_SEPARATOR .'job'. DIRECTORY_SEPARATOR;

    const DIRECTORY_SEPARATOR = '/';

    const CUSTOMER_IMAGE_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'uploadedImage'. SELF::DIRECTORY_SEPARATOR;

    const STATIC_STRING = '<svg xmlns="http://www.w3.org/2000/svg"></svg>';

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;


    protected $_baseDirectory;

    protected $_fileName;

    protected $_outPutPath;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    protected $_file;

    /**
     * File Uploader factory
     *
     * @var \Magento\Framework\Io\File
     */
    protected $_ioFile;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Init
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(
        \Magento\Framework\Filesystem $filesystem,
        File $file,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        $rootDirBasePath = DirectoryList::MEDIA
    ){
        $this->filesystem = $filesystem;
        $this->_file = $file;
        $this->_storeManager = $storeManager;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_baseDirectory = $filesystem->getDirectoryWrite(DirectoryList::PUB);
        $this->_urlBuilder = $urlBuilder;
        $this->_fileFactory = $fileFactory;
        $this->_ioFile = $ioFile;
        $this->rootDirBasePath = $rootDirBasePath;
    }

    public function getBaseUrl()
    {
        return $this->_urlBuilder->getBaseUrl();
    }

    public function getBasePath()
    {
        return $this->_baseDirectory->getAbsolutePath();
    }

    public function getMediaPath()
    {
        return $this->_mediaDirectory->getAbsolutePath();
    }

    public function getMediaUrl()
    {
        return $this->_urlBuilder->getBaseUrl(
            ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
        );
    }

    public function getDnbTempDir($time)
    {
        $tempDir = self::DNB_TEMP_PATH . 'temp-'. $time;
        $dir = $this->_mediaDirectory->getAbsolutePath($tempDir) . DIRECTORY_SEPARATOR;
        $destinationPath = $this->_mediaDirectory->getRelativePath($tempDir);
        $this->_mediaDirectory->create($destinationPath);
        return $dir;
    }

    public function getDnbTempPath()
    {
        return self::DNB_TEMP_PATH;
    }

    public function getDnbTempUrl($time)
    {
        $tempDir = self::DNB_TEMP_PATH . 'temp-'. $time;
        return $this->_urlBuilder->getBaseUrl(
            ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
        ) . $tempDir . '/';
    }

    public function getCartDesignsUrl()
    {
        return $this->getMediaUrl().self::CART_IMAGE_PATH;
    }

    public function getCartDesignsDir()
    {
        $directory = $this->_mediaDirectory->getAbsolutePath(self::CART_IMAGE_PATH);
        $this->createDirectory($directory);
        return $directory;
    }

    public function getDesignNBuyDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::DESIGNNBUY_PATH);
    }

    public function getOutputDir()
    {
        $directory = $this->_mediaDirectory->getAbsolutePath(self::OUTPUT_IMAGE_PATH);
        $this->createDirectory($directory);
        return $directory;
    }

    public function getCustomerDesignsDir()
    {
        $directory = $this->_mediaDirectory->getAbsolutePath(self::CUSTOMER_SAVED_DESIGNS_PATH);
        $this->createDirectory($directory);
        return $directory;
    }

    public function getCustomerDesignsUrl()
    {
        return $this->getMediaUrl().self::CUSTOMER_SAVED_DESIGNS_PATH;
    }

    public function getCustomerImageDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::CUSTOMER_IMAGE_PATH);
    }

    public function getCustomerImageUrl()
    {
        $customerImagePath = str_replace('\\','/',self::CUSTOMER_IMAGE_PATH);
        return $this->getMediaUrl().$customerImagePath;
    }

    public function getTemplateDesignsDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::TEMPLATE_DESIGN_PATH);
    }

    public function getDesignIdeaDesignsDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::DESIGNIDEA_DESIGN_PATH);
    }

    public function generatePng($request)
    {
        $svg = $request['svg'];

        if(isset($request['user']) && !empty($request['user']) && $request['user'] == 'admin'){
            $user = $request['user'];
        } else {
            $user = '';
        }
        $svg = str_replace('<?xml version="1.0" encoding="UTF-8"?>', " ", $svg);
        if($svg == "undefined" || trim($svg) == ""){
            $svg = self::STATIC_STRING;
        }

        $time = $request['current_time'];
        $side = '';
        if(isset($request['side'])){
            $side = $request['side']. '_';
        }
        //$side = $request['side'];

        if(isset($request['image_type']) && $request['image_type'] == 'product_image') {
            $suffix = 'product';
        } else {
            $suffix = 'design';
        }
        //$time = time();
        $svgFileName = $side . $time . '_' . $suffix . '.svg';
        $pngFileName = $side . $time . '_' . $suffix . '.png';

        $outPutPath = $this->getDnbTempDir($time);

        $this->customizeSVG($outPutPath, $svgFileName, $svg, $time, $user);

        /**************** Textarea bold font space issue resolved *****************/
        if(file_exists($outPutPath. $svgFileName)){
            
            $svgfileContents = file_get_contents($outPutPath. $svgFileName);
            
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = False;
            $doc->loadXML($svgfileContents);
            foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element){
                
                foreach ($element->getElementsByTagName("*") as $tags){
                    if($tags->localName=='text' && $tags->getAttribute('type') == 'advance'){
                        
                        $children = $tags->childNodes;
                        foreach( $children as $child ) {
                            $child->removeAttribute('dx');
                            $child->setAttribute('x',$child->getAttribute('bx'));
                        }
                    }
                }
            }
            
            $doc->save($outPutPath.$svgFileName);
            
        }
        /**************** Textarea bold font space issue resolved *****************/
        
        /*For Generate Filter Image Start*/
        $pngPath = '';
        $type = '';
        if(isset($request) && !empty($request) && array_key_exists('type', $request)){
            $type = $request['type'];
            if($type == 'filterimage'){
                $pngFileName = $side . $time . '_' . 'filtered' . '.png';
                $pngPath = $this->getCustomerImageDir();
                if(isset($request) && !empty($request) && array_key_exists('oldimagepath', $request) && $request['oldimagepath'] != ''){
                    $oldImageName = pathinfo($request['oldimagepath'], PATHINFO_BASENAME);
                    $this->deleteFile($pngPath.$oldImageName);
                }
            }
        }
        /*For Generate Filter Image End*/

        $this->convertSVGToPNG($outPutPath, $svgFileName, $pngFileName, $pngPath);

        if(isset($request['is_3d']) && $request['is_3d'] == true){
            $mapImageUrl = $request['map_image'];
            return $this->generate3DPreviewImage($outPutPath, $pngFileName, $mapImageUrl, $request);
        }
        $urlPath = $this->getDnbTempUrl($time);
        /*For Generate Filter Image Start*/
        if($type == 'filterimage'){
            $urlPath = $this->getCustomerImageUrl();
        }
        /*For Generate Filter Image End*/

        return $urlPath.$pngFileName;
    }

    public function generate3DPreviewImage($outputPath, $designImageName, $mapImageUrl, $request)
    {
        /*$productModel = Mage::getModel('catalog/product');
        $product = $productModel->load($productId);*/

        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        /*if($mapImageUrl == '') {
            $mapImageUrl = Mage::getModel('catalog/product_media_config')->getMediaUrl($product->getMapImage());
        }*/

        //$configArea = Mage::getModel('threed/configarea')->load($productId,'product_id');
        $configX = 0;
        $configY = 0;
        if(isset($request)){
            /*$configX = $request['xval'];
            $configY = $request['yval'];
            $configWidth = floatval($request['designWidth']) * 15;
            $configHeight = floatval($request['designHeight']) * 15;*/

            $threedConfigureArea = json_decode($request['threed_configure_area'], true);
            $configX = $threedConfigureArea['pos_x'];
            $configY = $threedConfigureArea['pos_y'];
            $configWidth = floatval($threedConfigureArea['width']);
            $configHeight = floatval($threedConfigureArea['height']);
        }


        $mapImagePath = $this->getBasePath().str_replace("/",DIRECTORY_SEPARATOR,strstr($mapImageUrl,'/media'));
        $type = pathinfo($mapImagePath, PATHINFO_EXTENSION);
        $type = strtolower($type);
        if($type == 'jpg' || $type == 'jpeg'){
            $mapImage = imagecreatefromjpeg($mapImagePath);
        } else if($type == 'png'){
            $mapImage = imagecreatefrompng($mapImagePath);
        }
        //$mapImage = imagecreatefrompng($mapImagePath);
        $mapWidth = imagesx($mapImage);
        $mapHeight = imagesy($mapImage);
        // Calculate multiplier based on the width of map and design tool product width which is fixed as 400
        $multiplier = $mapWidth/400;

        $rectWidth = $configWidth * $multiplier;
        $rectHeight = $configHeight * $multiplier;

        $designImage = imagecreatefrompng($outputPath . $designImageName);
        $width = imagesx($designImage);
        $height = imagesy($designImage);

        imagealphablending($designImage, false);
        imagesavealpha($designImage, true);
        // Resize design to configure area size
        $resizeDesign = imagecreatetruecolor($rectWidth, $rectHeight);
        imagesavealpha($resizeDesign, true);
        imagefill($resizeDesign, 0,0,0x7fff0000);
        imagecopyresampled($resizeDesign, $designImage, 0, 0, 0, 0, $rectWidth, $rectHeight, $width, $height);
        imagealphablending($designImage, true);
        imagepng($resizeDesign, $outputPath.'design.png');
        // Create rectangle based on configure area
        $rectDesign = imagecreatetruecolor($rectWidth, $rectHeight);

        /*if($isMulticolor != 1){
            $colorId = $pricingData['colorId'];
            $productModel = Mage::getModel('catalog/product');
            $colorAttribute = $productModel->getResource()->getAttribute("color");

            if ($colorAttribute->usesSource()) {
                $allOptions = $colorAttribute->getSource()->getAllOptions(true,true);
                foreach ($allOptions as $option) {
                    if($option['value'] == $colorId) {
                        $colorLabel = $option['label'];
                        break;
                    }
                }
                //$colorLabel = $colorAttribute->getSource()->getOptionText($colorId);
                $colorName = explode('(', $colorLabel);
                $colorText = $colorName[0];
                $colorTemp = array_reverse($colorName);
                $colorName = explode(')', $colorTemp[0]);
            }
            if(isset($request['colorCode']) && !empty($request['colorCode']) {
                $rgb = $this->hex2rgb($request['colorCode']);

                if(!empty($rgb)){
                    // Allocate color
                    $background = imagecolorallocate($rectDesign, $rgb[0], $rgb[1], $rgb[2]);
                    // fill the rect background with user selected color
                    imagefill($rectDesign, 0, 0, $background);
                }

            }else{
                imagefill($rectDesign,0,0,0x7fff0000);
            }
        }else{
            imagefill($rectDesign,0,0,0x7fff0000);
        }*/

        if(isset($request['colorCode']) && $request['colorCode'] != '') {
            $rgb = $this->hex2rgb($request['colorCode']);

            if(!empty($rgb)){
                // Allocate color
                $background = imagecolorallocate($rectDesign, $rgb[0], $rgb[1], $rgb[2]);
                // fill the rect background with user selected color
                imagefill($rectDesign, 0, 0, $background);
            }

        }else{
            imagefill($rectDesign,0,0,0x7fff0000);
        }

        $rectn = $outputPath.'rect.png';
        imagepng($rectDesign,$rectn);
        // Merge resized design and configured area size rectangle
        imagecopy($rectDesign, $resizeDesign,  0, 0, 0, 0, $rectWidth, $rectHeight);
        $rectName = $outputPath.'rectDesign.png';
        imagepng($rectDesign,$rectName);

        $rectX1 = $configX;
        $rectY1 = $configY;
        $rectX2 = $rectX1 + $rectWidth;
        $rectY2 = $rectY1 + $rectHeight;
        imagealphablending($mapImage, true);
        imagesavealpha($mapImage, true);
        // Merge map image and design image(resized design and configured area size rectangle)
        //imagecopy($mapImage, $rectDesign,  $rectX1 * $multiplier, $rectY1 * $multiplier, 0, 0, $rectWidth, $rectHeight);
        imagecopy($mapImage, $rectDesign,  $rectX1 * $multiplier, $rectY1 * $multiplier, 0, 0, $rectWidth, $rectHeight);
        imagealphablending($mapImage, false);
        imagesavealpha($mapImage, true);
        $imageName = $outputPath.rand().'.png';
        imagepng($mapImage,$imageName);
        imagedestroy($resizeDesign);
        imagedestroy($rectDesign);
        imagedestroy($mapImage);
        //$contents = ob_get_contents(); //Instead, output above is saved to $contents
        //ob_end_clean(); //End the output buffer.
        $dataUri =  $this->base64_encode_image($imageName,'');
        //unlink($imageName);
        return $dataUri;
    }

    public function base64_encode_image($filename=string, $filetype=string) {
        if ($filename) {
            $path = $this->getBasePath().str_replace("/",DIRECTORY_SEPARATOR, strstr($filename,'/media'));
            $type = pathinfo($path, PATHINFO_EXTENSION);

            $data = file_get_contents($path);
            //$type = pathinfo($file, PATHINFO_EXTENSION);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            return $base64;
        }
    }

    public function customizeSVG($outPutPath, $svgFileName, $svgContent, $time, $user = '', $sideId = '', $customerData = array())
    {
        $mediaPath = $this->getMediaPath();
        $this->_outPutPath = $this->getDnbTempDir($time);
        if ($svgContent != '') {
            $doc = new \DOMDocument();
            $doc->preserveWhiteSpace = False;
            $doc->loadXML($svgContent);

            $xpath = new \DOMXpath($doc);

            $nodes = array_reverse(
                iterator_to_array(
                    $xpath->evaluate('//*')
                )
            );

            foreach ($nodes as $node) {
                if($node->localName == 'svg'){
                    $replacement = $doc->createElementNS('http://www.w3.org/2000/svg', $node->localName);
                    foreach ($xpath->evaluate('node()|@*', $node) as $childNode) {
                        $replacement->appendChild($childNode);
                    }
                    $node->parentNode->replaceChild($replacement, $node);
                }
            }


            foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
                foreach ($element->getElementsByTagName("*") as $tags) {

                    if(is_array($customerData) && !empty($customerData) && $tags->localName == 'text' && $tags->getAttribute('sfid') != ''){
                        $sfId = $tags->getAttribute('sfid');
                        if(isset($customerData) && !empty($customerData) && array_key_exists($sfId, $customerData) && $customerData[$sfId] != ''){
                            $tags->nodeValue = $customerData[$sfId];
                        }
                    }

                    if(is_array($customerData) && !empty($customerData) && ($tags->localName == 'image' || $tags->localName == 'g') && $tags->getAttribute('sfid') != ''){
                        $sfId = $tags->getAttribute('sfid');
                        if(isset($customerData) && array_key_exists($sfId, $customerData) && $customerData[$sfId] != ''){
                            if($tags->localName == 'image'){
                                $tags->setAttribute('xlink:href', $customerData[$sfId]);
                            }
                            if($tags->localName == 'g'){
                                $tags->firstChild->firstChild->setAttribute('xlink:href', $customerData[$sfId]);
                            }
                        }
                    }


                    if ($tags->localName == 'image' && $tags->getAttribute('xlink:href') != '') {

                        if ($tags->getAttribute('class') == 'main_image') {
                            if($sideId != ''){
                                if ($tags->getAttribute('id') == $sideId) {
                                    $tags->setAttribute("display", 'block');
                                    $tags->setAttribute("style", '');
                                } else {
                                    $tags->setAttribute("style", 'display:none');
                                    $tags->setAttribute("display", 'none');
                                }
                            }
                        }

                        if (($tags->getAttribute('isAdminUploaded') == 'true' || $tags->getAttribute('isadminuploaded') == 'true') && $user != 'admin' && empty($customerData)) {
                            if ($tags->parentNode->nodeName == 'g' && $tags->parentNode->parentNode->getAttribute("type") == "photobox") {
                                $tags->parentNode->setAttribute("display", "none");
                                $tags->parentNode->parentNode->setAttribute("display", "none");
                            }
                        }
                        $imageUrl = $tags->getAttribute('xlink:href');
                        $imageUrl = str_replace("\\", "/", $imageUrl);
                        $name = pathinfo($imageUrl, PATHINFO_BASENAME);

                        if ($name == 'dragImage.svg') {
                            $tags->parentNode->setAttribute("display", "none");
                        } else {
                            $uploadedImage = explode('media/', $imageUrl);
                            $tags->setAttribute('xlink:href', $name);
                            if (isset($uploadedImage) && !empty($uploadedImage) && !empty($uploadedImage[1]) && $uploadedImage[1] != '') {
                                $file = $mediaPath . $uploadedImage[1];
                                if(file_exists($file)) {
                                    copy($file, $outPutPath.$name);
                                }
                            }
                        }
                    }
                    if($tags->getAttribute("type") == "textarea" || $tags->getAttribute("type") == "advance"){
                        foreach ($tags->childNodes as $node) {
                            if ($node->nodeName == 'rect') {
                                $node->setAttribute("display", 'none');
                            }
                        }
                    }
                }
            }
            $doc->save($outPutPath . $svgFileName, $time);
        }
    }

    public function convertSVGToPNG($outputImagePath, $svgFileName, $pngFileName, $pngPath = '')
    {
        $svg = $outputImagePath . $svgFileName;

        if(file_exists($svg)) {

            
            $inkscape = new Inkscape($svg);
            //$inkscape->setDpi(96);
            try{
                if($pngPath != ''){
                    $outputImagePath = $pngPath;
                }
                if($_SERVER['HTTP_HOST'] != "127.0.0.1"){
                    $ok = $inkscape->export( 'png', $outputImagePath . $pngFileName );
                    return $ok;
                } else {
                    shell_exec("inkscape -z ".$svg." -e ". $outputImagePath . $pngFileName);
                }
            } catch (\Exception $e) {
                //echo $e->getMessage(); exit;
            }
        }

    }

    public function convertSVGToPDF($outputImagePath, $svgFileName, $pdfFileName)
    {
        $svg = $outputImagePath . $svgFileName;

        if(file_exists($svg)) {
            $inkscape = new Inkscape($svg);
            //$inkscape->exportAreaSnap(); //better pixel art
            //$inkscape->exportTextToPath();
            // $inkscape->setSize($width=792, $height=408);
            // $inkscape->setDpi(96);
            try{
                $ok = $inkscape->export( 'pdf', $outputImagePath . $pdfFileName );
                return $ok;
            } catch (\Exception $e) {
            }
        }
    }

    public function saveStringOnServer($request)
    {

    }

    public function saveBase64($path, $base64)
    {
        $img = str_replace('data:image/png;base64,', '', $base64);
        $img = str_replace(' ', '+', $img);
        $imgdata = base64_decode($img);
        $srcImgdata = imagecreatefromstring($imgdata);
        if ($srcImgdata) {
            
            $width = imagesx($srcImgdata);
            $height = imagesy($srcImgdata);

                if($width > 500 || $height > 500) {
                    $resizedImage = $this->resize_image($imgdata, 500, 500);
                    file_put_contents($path, $resizedImage);
                } else {
                    file_put_contents($path, $imgdata);
                }
        } else {
            
            file_put_contents($path, $imgdata);
        }
    }
    public function resize_image($file, $w, $h, $crop=FALSE) {
        //list($width, $height) = getimagesize($file);
        $src = imagecreatefromstring($file);
        if (!$src) return false;
        $width = imagesx($src);
        $height = imagesy($src);
      
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
              $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
              $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
              $newwidth = $h*$r;
              $newheight = $h;
            } else {
              $newheight = $w/$r;
              $newwidth = $w;
            }
        }
        //$src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);
            
            /********** transparent ************/
            imagealphablending($dst, false);
            imagesavealpha($dst,true);
            $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
            imagefilledrectangle($dst, 0, 0, $newwidth, $newheight, $transparent);
            /********** transparent end ************/

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
      
        // Buffering
      ob_start();
      imagepng($dst);
      $data = ob_get_contents();
      ob_end_clean();
        return $data;
      }
      
    public function saveSvg($path, $svg)
    {
        if($svg != ''){
            $svgImage = urldecode($svg);
            file_put_contents($path, $svgImage);
        }
    }

    protected function fileExists($filename)
    {
        return $this->_mediaDirectory->isFile($filename);
    }

    protected function isDirectoryExists($path)
    {
        return $this->_mediaDirectory->isDirectory($path);
    }

    protected function createDirectory($directory)
    {
        if ( ! $this->isDirectoryExists($directory)) {
            $this->_ioFile->mkdir($directory, 0777);
        }
    }

    public function removeTempDirectory($dir)
    {
        $this->_mediaDirectory->delete($dir);
    }

    public function deleteFile($file)
    {
        if ($this->_file->isExists($file)) {
            $this->_file->deleteFile($file);
        }
    }

    public function getFormatedSVG($svg)
    {
        $webpath = $this->getBaseUrl();
        $svgContent = file_get_contents($svg);
        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = False;
        if($svgContent != ''){
            $doc->loadXML($svgContent);
            foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element):
                foreach ($element->getElementsByTagName("*") as $tags):
                    if($tags->localName=='pattern' && $tags->getAttribute('id')=='gridpattern') {
                        $tags->parentNode->removeChild( $tags );
                    }
                    if($tags->localName=='image' && $tags->getAttribute('xlink:href')!=''):
                        $imageUrl = $tags->getAttribute('xlink:href');
                        $uploadedImage = explode('pub/media/',$imageUrl);
                        if(!empty($uploadedImage) && $uploadedImage[0]!='' && $uploadedImage[0] != $webpath && !empty($uploadedImage[1])):
                            $tags->setAttribute('xlink:href', $this->getMediaUrl().$uploadedImage[1]);
                            if ($tags->hasAttribute('templateSrc')){
                                $tags->setAttribute('templateSrc', $this->getMediaUrl().$uploadedImage[1]);
                            } else {
                                $tags->setAttribute('templatesrc', $this->getMediaUrl().$uploadedImage[1]);
                            }
                        endif;
                    endif;
                endforeach;
            endforeach;
        }
        return $doc->saveXML($doc);
    }

    public function convertUnit($value, $unit){
        if($unit == 'px'){
            $value = ($value*25.4)/96;
            return $value;
        }else if($unit == 'in'){
            $value = $value/0.03937;
            return $value;
        }else if($unit == 'cm'){
            $value = $value * 10;
            return $value;
        }else{
            return $value;
        }
    }

    /**
     * Custom options downloader
     *
     * @param array $info
     * @return void
     * @throws \Exception
     */
    public function downloadFile($info)
    {
        $relativePath = $info['path'];
        /* echo $this->_mediaDirectory->getAbsolutePath($relativePath).'<br/>';
        echo $this->rootDirBasePath; exit; */
        $this->_fileFactory->create(
            $info['title'],
            ['value' => $this->_mediaDirectory->getAbsolutePath($relativePath), 'type' => 'filename'],
            $this->rootDirBasePath
        );
    }


    function hex2rgb($hex) {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3) {
            $r = hexdec(substr($hex,0,1).substr($hex,0,1));
            $g = hexdec(substr($hex,1,1).substr($hex,1,1));
            $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
            $r = hexdec(substr($hex,0,2));
            $g = hexdec(substr($hex,2,2));
            $b = hexdec(substr($hex,4,2));
        }
        $rgb = array($r, $g, $b);
        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public function getJobDesignsDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::JOB_DESIGNS_PATH);
    }

    public function getJobDesignsUrl()
    {
        return $this->getMediaUrl().self::JOB_DESIGNS_PATH;
    }

    public function mergeDesignProductImages($sourceImage, $destinationImage, $pngName, $configArea)
    {
        try {
            if(array_key_exists('x', $configArea)){
                $x = $configArea['x'];
            } else {
                $x = $configArea['pos_x'];
            }
            if(array_key_exists('y', $configArea)){
                $y = $configArea['y'];
            } else {
                $y = $configArea['pos_y'];
            }
            $dst_im = imagecreatefrompng($destinationImage);
            $src_im = imagecreatefrompng($sourceImage);
            if(array_key_exists('for_preview', $configArea) && $configArea['for_preview']){
                $srcHeight = $configArea['height'];
                $srcWidth = $configArea['width'];
            } else {
                list($srcWidth, $srcHeight) = getimagesize($sourceImage);
            }
            //Adjust paramerters according to your image
            $this->imageCopyMergeAlpha($dst_im, $src_im, $x, $y, 0, 0, $srcWidth, $srcHeight);
            ob_start();
            imagepng($dst_im, $pngName);
            $imagestring = ob_get_contents();
            ob_end_clean();
        } catch (\Exception $e) {
            echo "getMessage" . $e->getMessage();
        }
        return;
    }


    public function imageCopyMergeAlpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h)
    {
        imagealphablending($dst_im, true);
        imagesavealpha($dst_im, true);

        imagecopy($dst_im, $src_im, $dst_x, $dst_y, 0, 0, $src_w, $src_h);
    }

    public function generateCustomerPreviewFromDesign($request)
    {
        $pngImages = array();
        $designSvgNames = array();

        $configArea = [];
        if(array_key_exists('productImageSvg',$request) && isset($request['productImageSvg']) && !empty($request['productImageSvg'])) {
            if(array_key_exists('configarea',$request) && isset($request['configarea']) && !empty($request['configarea'])) {
                $configArea = json_decode(stripslashes($request['configarea']), true);
            }
        }

        $cnt = 0;

        $timeStamp = time();
        $outPutPath = $this->getDnbTempDir($timeStamp);

        if(isset($request['svg']) && !empty($request['svg'])){
            $svgStr = $request['svg'];

            foreach($svgStr as $svg){
                $svgImageName = "design_".$timeStamp.'_'.$cnt.'.svg';
                $designSvgNames[$cnt] = $svgImagePath = $outPutPath . $svgImageName;

                /*Generate png image side from svg */
                if(isset($request['productImageSvg']) && !empty($request['productImageSvg'])) {
                    $this->saveSvg($svgImagePath, $svg);
                    $designPngName = "svg_".$timeStamp.'_'.$cnt.'.png';
                    $this->customizeSVG($outPutPath, $svgImageName, urldecode($svg), $timeStamp, '', '', $request['customerDetails']['data']);
                    $this->convertSVGToPNG($outPutPath, $svgImageName, $designPngName);

                    $pngImages[$cnt]['design'] = $outPutPath.$designPngName;

                    $productPngName = "product_".$timeStamp.'_'.$cnt.'.png';
                    $productSvgName = "product_".$timeStamp.'_'.$cnt.'.svg';
                    $this->customizeSVG($outPutPath, $productSvgName, urldecode($request['productImageSvg']), $timeStamp, '', 'img_'.$cnt);
                    $this->convertSVGToPNG($outPutPath, $productSvgName, $productPngName);
                    $pngImages[$cnt]['product'] = $outPutPath.$productPngName;
                    $designName = "design_".$timeStamp.'_'.$cnt.'.png';
                    $result['png'][$cnt] = $designName;
                    $area = $configArea[$cnt];
                    //$area['for_preview'] = true;
                    $area['for_preview'] = false;
                    $this->mergeDesignProductImages($outPutPath.$designPngName, $outPutPath.$productPngName, $outPutPath.$designName, $area);
                } else {
                    $designName = "design_".$timeStamp.'_'.$cnt.'.png';
                    $this->customizeSVG($outPutPath, $svgImageName, urldecode($svg), $timeStamp, '', '', $request['customerDetails']['data']);
                    $this->convertSVGToPNG($outPutPath, $svgImageName, $designName);
                }
                $cnt++;
                break;
            }
        }
        return $outPutPath.$designName;
    }

}

