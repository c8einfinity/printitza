<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Base\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use DOMDocument;
use Designnbuy\Base\Service\Inkscape as Inkscape;
use Designnbuy\Base\Service\Ghostscript as Ghostscript;
//use Designnbuy\Base\Service\FTPClient as FTPClient;
use Designnbuy\Base\Service\FTPUploader as FTPUploader;
use Magento\Framework\App\Filesystem\DirectoryList;

use TCPDF;
use FPDF;
use FPDI;


//use TCPDF_TCPDF;
//use FPDI_FPDI;

class Output implements ObserverInterface
{
    protected $customerSession;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $outputHelper;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $dnbBaseHelper;


    /**
     * @var \Designnbuy\HotFolder\Helper\Data
     */

    private $hotFolderHelper;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $_orderInterface;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;


    /**
     * @var \Designnbuy\Customer\Model\ResourceModel\Image\CollectionFactory
     */
    protected $_customerImageCollectionFactory;

    private $orderRepository;

    protected $_productAttributeSetId  = null;
    protected $_product;
    protected $_item;
    protected $_order;
    protected $_productId  = null;
    protected $_productOptions  = null;
    protected $_printingMethod  = null;
    protected $_customCanvasAttributeSetId  = null;
    protected $_customProductAttributeSetId  = null;
    protected $_unit  = 'mm';
    protected $_outputFormat;
    protected $_orderInformationFormat = 'xml';
    protected $_customerId = null;
    protected $_sourceImagePath = null;
    protected $_outputFor = null;
    protected $_fromAdmin = false;
    protected $_destinationFolder = null;
    protected $_zipfolderName = null;
    protected $_orderIncrementId = null;
    protected $_side = 0;
    protected $_spotColorOutput = false;

    protected $_usedHexColorCodes = [];

    protected $_printableColors = [];

    protected $sizeFactory;

    protected $albumHelper;

    /**
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Designnbuy\HotFolder\Helper\Data $hotFolderHelper,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Designnbuy\Customer\Model\ResourceModel\Image\CollectionFactory $customerImageCollectionFactory,
        \Designnbuy\Color\Model\Color $color,
        \Designnbuy\Sheet\Model\SizeFactory $sizeFactory,
        \Designnbuy\Font\Model\Font $font,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $albumHelper
    ) {
        $this->customerSession = $customerSession;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->outputHelper = $outputHelper;
        $this->hotFolderHelper = $hotFolderHelper;
        $this->_orderInterface = $orderInterface;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_customerImageCollectionFactory = $customerImageCollectionFactory;
        $this->_color = $color;
        $this->sizeFactory = $sizeFactory;
        $this->_font = $font;
        $this->fonts = null;
        $this->_filesystem = $filesystem;
        $this->albumHelper = $albumHelper;
    }

    /**
     * Subtract qtys of quote item products after multishipping checkout
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        if ($this->customerSession->isLoggedIn()) {
            $this->_customerId = $this->customerSession->getCustomerId();
        }
        /* @var $order Order */
        $this->_order = $order = $observer->getEvent()->getData('order');
        if($order){
            // Get the items from the order
            $items = $order->getAllVisibleItems();
            foreach ($items as $item) {
                $this->generateItemOutput($order->getIncrementId(), $item->getId(), 'automatic');
            }
        }

        $orders = $observer->getEvent()->getData('orders');
        if($orders){
            foreach ($orders as $order) {
                $this->_order = $order;
                // Get the items from the order
                $items = $order->getAllVisibleItems();
                foreach ($items as $item) {
                    $this->generateItemOutput($order->getIncrementId(), $item->getId(), 'automatic');
                }
            }
        }

        return $this;
    }

    /*public function generateOutput($order, $item, $output = 'automatic')
    {
        $orderEntityId = $order->getIncrementId();
        $this->_product = $item->getProduct();
        $this->_productAttributeSetId = $_product->getAttributeSetId();
        $this->_productOptions = $_item->getProductOptions();
        echo "getAttributeSetId<pre>";
        print_r($_product->getAttributeSetId());
        die;
    }*/


    public function generateItemOutput($orderId, $itemId, $orderArea = 'automatic')
    {
        $this->orderIncrementId = $orderId;
        $this->_spotColorOutput = false;
        $this->_item = $_item = $this->_orderItemFactory->create()->load($itemId);
        //$orderEntityId = $order->getIncrementId();
        $orderEntityId = $orderId;
        $this->_product = $_product = $_item->getProduct();

        if($_product->getAllowSpotColorOutput()){
            $this->_spotColorOutput = true;
            $this->_printableColors = $this->_color->getProductPrintableColors($_product->getEntityId());
        }
        $this->_productAttributeSetId = $_product->getAttributeSetId();
        $this->_productOptions = $_item->getProductOptions();

        $this->_customCanvasAttributeSetId = $this->dnbBaseHelper->getCustomCanvasAttributeSetId();
        $this->_customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();
        if($orderArea == 'manual'){
            $this->_fromAdmin = true;
        }

        $cartimagesDir = $this->outputHelper->getCartDesignsDir();

        $item = '';
        $productOptions = '';
        $savestr = '';
        //$_item = Mage::getModel('sales/order_item')->load($itemId);

        // $_product =  Mage::getModel('catalog/product')->load($_item->getProductId());

        $this->_destinationFolder = $this->hotFolderHelper->outputFolderLocation($orderEntityId, $_product);
        

        $this->_productAttributeSetId = $_product->getAttributeSetId();
        $this->_productOptions = $_item->getProductOptions();
        $this->_productOptions = $productOptions = $_item->getProductOptions();
        
        if(isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['svg'])){
            $savestr = $productOptions['info_buyRequest']['svg'];
            if(isset($productOptions['info_buyRequest']['output_for'])){
                $this->_outputFor = $productOptions['info_buyRequest']['output_for'];
                if(isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['customer_id'])){
                    $this->_customerId = $productOptions['info_buyRequest']['customer_id'];
                }

            }
        }

        /*
        * Output Area :- 0 = Automatic, 1 = Manual
        */
        $VDPOutputArea = 0;
        $VDPOutputArea = $this->dnbBaseHelper->getVDPOutputArea();

        if($this->_fromAdmin){
            $VDPOutputArea = 0;
        }

        if($savestr!=''):

            /* VDP Start vdp_output*/

            if($_product->getAllowVdp() == 1 && $VDPOutputArea == 0)
            {

                if(isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['vdp_file'])){
                    $vdpFile = $productOptions['info_buyRequest']['vdp_file'];
                    $svgFile = $productOptions['info_buyRequest']['svg_file'];
                    $vdpFileData = file_get_contents($cartimagesDir.$vdpFile);

                    $vdpData = array();
                    if($vdpFileData !='' && $vdpFileData !='undefined') {
                        $vdpData = json_decode($vdpFileData, true);
                    }

                    $svgFileData = file_get_contents($cartimagesDir.$svgFile);
                    $svgData = array();
                    if($svgFileData !='' && $svgFileData !='undefined') {
                        $svgData = json_decode($svgFileData, true);
                    }

                    $svgFiles = array();
                    $svgFiles = explode(",",$productOptions['info_buyRequest']['svg']);

                    $svgPage=0;
                    foreach($svgFiles as $OutputSvg){

                        for ($j = 0; $j < count($vdpData); $j++){
                            if($j !=0){
//									$pageData = split(",",$vdpData[$j][0]);
                                $pageData = $vdpData[$j];

                                $svgImage = $cartimagesDir . $OutputSvg;
                                if(file_exists($svgImage))
                                {
                                    $svgfileContents = file_get_contents($svgImage);
                                    $doc = new DOMDocument();
                                    $doc->preserveWhiteSpace = False;
                                    $doc->loadXML($svgfileContents);
                                    foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element):
                                        foreach ($element->getElementsByTagName("*") as $tags):
                                            for ($i = 0; $i <count($svgData); $i++) {
                                                $str = explode("_", $svgData[$i]['id']);
                                                $objID = $str[0]."_".$str[1];   /* svg Tag Id */
                                                $sideID = $str[3];   /* Page Side */
                                                if($sideID == $svgPage){
                                                    if($tags->getAttribute('id') == $objID && $tags->localName == "text" && $tags->getAttribute('isvdp')== "true"){
                                                        if($pageData[$svgData[$i]['index']-1]!=''){
                                                            $tags->nodeValue = $pageData[$svgData[$i]['index']-1];
                                                        } else {
                                                            $tags->setAttribute("display", "none");
                                                        }
                                                    } else if($tags->getAttribute('id')== $objID && $tags->localName == "image"){

                                                        if($pageData[$svgData[$i]['index']-1]!=''){
                                                            $pageData[$svgData[$i]['index']-1];
                                                            $tags->setAttribute('xlink:href',$pageData[$svgData[$i]['index']-1]);
                                                        } else {
                                                            $tags->setAttribute("display", "none");
                                                        }
                                                    } else if($tags->getAttribute('id')== $objID && $tags->localName == "g" && $tags->getAttribute('type') == 'photobox'){

                                                        if($pageData[$svgData[$i]['index']-1] != ''){
                                                            $tags->firstChild->firstChild->setAttribute('xlink:href',$pageData[$svgData[$i]['index']-1]);
                                                        } else {
                                                            $tags->setAttribute("display", "none");
                                                        }
                                                    }
                                                } /* Page side compare */
                                            }
                                        endforeach;
                                    endforeach;
                                    $vdptimeStamp = time();
                                    $svg = $doc->saveXML();
                                    $svgImage = urldecode($svg);
                                    $filename = 'Vdp_'.$vdptimeStamp.'_'.$svgPage.'_'.$j.'.svg';
                                    $svgOutputFiles[] = $filename;
                                    $svgImagePath = $cartimagesDir.$filename;
                                    $f = file_put_contents($svgImagePath,$svgImage);
                                    chmod($svgImagePath,0777);
                                }
                            }
                        }
                        $svgPage++;
                    }   /* svg end*/
                }    /* End is vdp data check */
                else {
                    $svgOutputFiles = explode(",",$savestr);
                }
            }
            else {
                $svgOutputFiles = explode(",",$savestr);
            }
            /* VDP End */

            if (($_product->getType_id() == 'simple') && ($_product->getAttributeSetId()== $this->_customCanvasAttributeSetId)):
                $this->_orderInformationFormat = $this->dnbBaseHelper->getCanvasOrderInformationFormat();
                $this->_outPutFormat = $this->dnbBaseHelper->getCanvasOutputFormat();
                $this->generateSVG($svgOutputFiles,$orderEntityId,$itemId);
                if(strtoupper($this->_outPutFormat) == "BOTH" || strtoupper($this->_outPutFormat) == "PDF"):
                    $this->generatePdf($svgOutputFiles,$orderEntityId,$itemId);
                endif;
                $this->zipOutputFiles($orderEntityId,$itemId,$_item->getProductId());
            endif;

            if (($_product->getIsCustomizable()) && ($_product->getAttributeSetId()== $this->_customProductAttributeSetId)):
                $this->_orderInformationFormat = $this->dnbBaseHelper->getMerchandiseOrderInformationFormat();
                if(isset($productOptions['info_buyRequest']['printingMethod'])){
                    $printingMethod = json_decode($productOptions['info_buyRequest']['printingMethod'], true);
                    if(isset($printingMethod['isCustomized'])){
                        $customizedSides = array_values($printingMethod['isCustomized']);
                        $svgFiles = array_values($svgOutputFiles);
                        $svgOutputFiles = array();
                        foreach($customizedSides as $key => $side){
                            if($side == 1){
                                $svgOutputFiles[] = $svgFiles[$key];
                            }
                        }
                    }
                }
                $totalName = 0;
                $totalNumber = 0;
                /*For Name Number Start*/
                if(isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['nameNumber']) && !empty($productOptions['info_buyRequest']['nameNumber'])) {
                    $nameNumber = (array)$productOptions['info_buyRequest']['nameNumber'];

                    if (isset($nameNumber['data']) && !empty($nameNumber['data'])) {
                        $totalName = $nameNumber['totalname'];
                        $totalNumber = $nameNumber['totalnumber'];
                        $nameNumberFile = $nameNumber['data'];

                        $nameNumberFileData = file_get_contents($cartimagesDir . $nameNumberFile);
                        if ($nameNumberFileData != '' && $nameNumberFileData != 'undefined') {
                            //$design['nameNumber'] = json_decode($nameNumberFileData, true);
                            $nameNumberData = json_decode(htmlspecialchars_decode($nameNumberFileData), true);

                            if ($totalName > 0 || $totalNumber > 0) {
                                //$namenumberdata = json_decode(htmlspecialchars_decode($nameNumberDataString), true);

                                $nameArray = array();
                                $numberArray = array();

                                if (!empty($nameNumberData)) {
                                    foreach ($nameNumberData as $n) {
                                        if (in_array($n['id'], $productOptions['info_buyRequest']['super_attribute'])) {
                                            if(!empty( $n['nameData'] )){
                                                $nameDataString = array(
                                                    'd' => $n['nameData'][0]
                                                );
                                            } else {
                                                $nameDataString = array(
                                                    'd' => ''
                                                );
                                            }

                                            if(!empty( $n['numberData'] )){
                                                $numberDataString = array(
                                                    'd' => $n['numberData'][0]
                                                );
                                            } else {
                                                $numberDataString = array(
                                                    'd' => ''
                                                );
                                            }
                                            $nameArray[] = $nameDataString;
                                            $numberArray[] = $numberDataString;
                                            /*$nameArray[] = array(
                                                'd' => $n['nameData'][0]
                                            );
                                            $numberArray[] = array(
                                                'd' => $n['numberData'][0]
                                            );*/
                                        }
                                    }
                                }
                            }
                        }
                        $nameNumberData = '';
                    }
                }

                /*Name Number End*/

                $this->_outPutFormat = $this->dnbBaseHelper->getMerchandiseOutputFormat();
                $this->generateSVG($svgOutputFiles,$orderEntityId,$itemId);
                
                if(strtoupper($this->_outPutFormat) == "BOTH" || strtoupper($this->_outPutFormat) == "PDF"):
                    $this->generatePdf($svgOutputFiles,$orderEntityId,$itemId);
                endif;

                /*For Name Number Start*/
                //$this->generateSVG($svgOutputFiles,$orderEntityId,$itemId);
                $this->_side = 0;
                if ($totalName > 0 || $totalNumber > 0) {
                    $svgNameNumberArray = [];
                    $svgNameNumberArray = $this->generateSVG($svgOutputFiles, $orderEntityId, $itemId, $nameArray, $numberArray);
                    foreach ($svgNameNumberArray as $sideKey => $svgNameNumber) {
                        if (is_array($svgNameNumberArray) && !empty($svgNameNumberArray)) {
                            if (strtoupper($this->_outPutFormat) == "BOTH" || strtoupper($this->_outPutFormat) == "PDF"):
                                $this->generatePdf($svgNameNumber, $orderEntityId, $itemId, $sideKey);
                            endif;
                        }
                    }
                }
                /*For Name Number End*/

                $this->zipOutputFiles($orderEntityId,$itemId,$_item->getProductId());
            endif;
        endif;
        if(isset($this->_productOptions['info_buyRequest']['attachment'])){
            if(isset($this->_productOptions['info_buyRequest']['attachment']['fileName'])){
                if(!empty($this->_productOptions['info_buyRequest']['attachment']['fileName'])){
                    $this->zipOutputFiles($orderEntityId,$itemId,$_item->getProductId());
                }
            }
        }
        if(isset($this->_productOptions['info_buyRequest']['cart_photo_id']) && isset($this->_productOptions['info_buyRequest']['photoAlbum_FileName'])){
            if($this->_productOptions['info_buyRequest']['cart_photo_id'] != "" && $this->_productOptions['info_buyRequest']['photoAlbum_FileName'] != ""){
                //echo "<pre>"; print_r($this->_productOptions['info_buyRequest']); exit;
                $photo_ids = $this->_productOptions['info_buyRequest']['cart_photo_id'];
                $photoAlbumFile = $this->_productOptions['info_buyRequest']['photoAlbum_FileName'];
                //$photos = json_decode($photo_ids,true);
                //echo "<pre>"; print_r($photos); exit;
                $this->albumHelper->zipPhotoAlbum($photo_ids,'',$photoAlbumFile,true,$this->_product,$orderEntityId);
            }
        }
    }

    public function generatePNG($svgOutputFiles,$orderEntityId,$itemId) {
        $svgFilesToZip = array();
        $imageFilesToZip = array();
        $uploadedImage = array();
        $pdfFilesToZip = array();
        $this->_sourceImagePath = $sourceImagePath = $this->outputHelper->getOutputDir()."order-".$orderEntityId."-".$itemId. DIRECTORY_SEPARATOR;
        //$this->_sourceImagePath = Mage::getBaseDir(). DIRECTORY_SEPARATOR .'media' . DIRECTORY_SEPARATOR .'output'. DIRECTORY_SEPARATOR ."order-".$orderEntityId."-".$itemId. DIRECTORY_SEPARATOR;
        //$inkscape = new INKSCAPE_INKSCAPE();
        //echo "getVersion".$inkscape->getVersion();
        //require_once(Mage::getBaseDir('lib') . '/Inkscape//Inkscape.php');
        foreach ($svgOutputFiles as $svgFile):
            $svgFileName = explode('.',$svgFile);
            if($svgFileName[1]=='svg'):
                $svgFilePath = $sourceImagePath.$svgFile;
                if (file_exists($svgFilePath))	{
                    $svgName = pathinfo($svgFilePath, PATHINFO_FILENAME);
                    $pdfName = $svgName.'.pdf';
                    $pdfFilesToZip[] = $pdfName;
                    $this->outputHelper->convertSVGToPDF($sourceImagePath, $svgFilePath, $pdfName);
                    /*$inkscape = new INKSCAPE_INKSCAPE($svgFilePath);
                    $inkscape->exportAreaSnap(); //better pixel art
                    $inkscape->exportTextToPath();
                    try
                    {
                        $ok = $inkscape->export( 'pdf', $sourceImagePath . $pdfName );
                    }
                    catch ( Exception $exc )
                    {
                        Mage::log("Inkscape Message".$exc->getMessage(), null, "dnb.log");
                        Mage::log("Inkscape TraceAsString".$exc->getTraceAsString(), null, "dnb.log");
                    }*/
                }
            endif;
        endforeach;
        return;
        // $zipFileName = "order-".$orderEntityId."-".$item_id.".zip";
        // $result = $this->zipFilesAndDownload($svgFilesToZip,$imageFilesToZip,$pdfFilesToZip,$zipFileName,$overwrite = false);
        // $this->generateZip($orderEntityId,$itemId);
        // return $this;
    }

    public function generatePdf($svgOutputFiles ,$orderEntityId, $itemId, $sideKey = -1) {
        ini_set('display_errors', 1);
        $svgFilesToZip = array();
        $imageFilesToZip = array();
        $uploadedImage = array();
        $pdfFilesToZip = array();
        $outputPNGFiles = array();
        $this->_sourceImagePath = $sourceImagePath = $this->outputHelper->getOutputDir()."order-".$orderEntityId."-".$itemId. DIRECTORY_SEPARATOR;

        $outputType = $this->dnbBaseHelper->getPDFOutputType();//RGB/CMYK
        //require_once(Mage::getBaseDir('lib') . '/Inkscape//Inkscape.php');
        $this->_side = 0;
        $rgbPdfs = [];
        $cmykPdfs = [];
        foreach ($svgOutputFiles as $svgFile):
            $svgFileName = explode('.',$svgFile);
            if($svgFileName[1]=='svg') {
                $svgFilePath = $sourceImagePath . $svgFile;
                if (file_exists($svgFilePath)) {

                    $svgName = pathinfo($svgFilePath, PATHINFO_FILENAME);
                    try {
                        $pdfTempName = $svgName . '-temp' . '.pdf';
                        $pdfName = $svgName . '.pdf';
                        $pdf = $sourceImagePath . $pdfName;
                        //$ok = $this->outputHelper->convertSVGToPDF($sourceImagePath, $svgFile, $pdfName);
                        $ok = $this->outputHelper->convertSVGToPDF($sourceImagePath, $svgFile, $pdfTempName);
                        $rgbPdfs[] = $pdf;
                        if ($ok) {
                            chmod($sourceImagePath . $pdfTempName, 0777);

                            /*resize generated pdf according to product size*/
                            //$this->resizeGeneratedPDF($svgFileName[0], $pdf);

                            $this->resizeGeneratedPDF($sourceImagePath, $pdfTempName, $pdfName, $sideKey);
                            /*Generate CMYK output PDF*/
                            if (strtoupper($outputType) == 'RGBCMYK') {
                                $CMYKPdfName = $svgName . '_CMYK' . '.pdf';
                                $CMYKPdf = $sourceImagePath . $CMYKPdfName;
                                //$this->generateCMYKPDF($pdf, $CMYKPdf);//GhostScript Output
                                $this->generateCMYKPDF_TCPDF($svgFile, $sourceImagePath, $CMYKPdf);
                                $cmykPdfs[] = $CMYKPdf;
                            }

                            if($this->_spotColorOutput){
                                $SPOTPdfName = $svgName . '_SPOT' . '.pdf';
                                $SPOTPdf = $sourceImagePath . $SPOTPdfName;
                                $this->generateCMYKPDF_TCPDF($svgFile, $sourceImagePath, $SPOTPdf, $spotColor = true);
                            }
                        }
                        $pngName = $svgName . '.png';
                        $png = $sourceImagePath . $pngName;

                        /*$inkscape = new INKSCAPE_INKSCAPE($svgFilePath);
                        $inkscape->exportAreaSnap(); //better pixel art
                        $inkscape->export( 'png', $png );*/
                        $ok = $this->outputHelper->convertSVGToPNG($sourceImagePath, $svgFile, $pngName);
                        array_push($outputPNGFiles, $pngName);
                    } catch (\Exception $exc) {

                    }

                }
            }
            $this->_side++;
        endforeach;

        /*Imposition Start*/
        if($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId && $this->_product->getNupSheetSize() != '' && $this->_product->getAllowNupOutput() == 1 ){
            /*Generate For RGB PDFs*/
            if(is_array($rgbPdfs) && !empty($rgbPdfs)){
               $this->generateNUpOutput($rgbPdfs, $orderEntityId, $itemId, $prefix = 'N-Up-RGB');
            }
            /*Generate For CMYK PDFs*/
            if(is_array($cmykPdfs) && !empty($cmykPdfs)){
                $this->generateNUpOutput($cmykPdfs, $orderEntityId, $itemId, $prefix = 'N-Up-CMYK');
            }
        }
        /*Imposition End*/

        $lowPDF = $this->outputHelper->getOutputDir() . "order-".$orderEntityId."-".$itemId.".pdf";
        if($sideKey == -1 ){
            $this->generateLowResolutionPdf($outputPNGFiles, $lowPDF, $sideKey);
        }

        return;
    }

    /*Imposition Start*/
    public function generateNUpOutput($pdfs, $orderEntityId, $itemId, $prefix)
    {
        $outputPdfPath = $this->outputHelper->getOutputDir() . "order-" . $orderEntityId . "-" . $itemId . DIRECTORY_SEPARATOR;
       
        if ($this->_product->getBaseUnit() != '') {
            $unit = $this->_product->getData('base_unit');
        } else {
            if($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId)
            $unit = $this->dnbBaseHelper->getBaseUnit();
            else
            $unit = $this->dnbBaseHelper->getmerchandiseBaseUnit();
        }

        $_item = $this->_orderItemFactory->create()->load($itemId);

        $nupSheetSize = $this->_product->getResource()->getAttribute('nup_sheet_size');
        $nupSheetSizeId = $this->_product->getNupSheetSize();

        if ($nupSheetSize->usesSource()) {
            $sheetSize = $nupSheetSize->getSource()->getOptionText($this->_product->getNupSheetSize());
        }

        list($pageWidth, $pageHeight) = explode("x", strtolower($sheetSize));

        $nupBleedMargin = 0.25;//$this->_product->getNupBleedMargin() ?? 0;
        
        if($this->_product->getNupBleedMargin() != "" && !$this->_product->getNupBleedMargin() <= 0){
            $nupBleedMargin =  $this->_product->getNupBleedMargin();
        }
        
        $bleedMargin = 0;
        if (($this->_product->getType_id() == 'simple') && ($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId)):
            $size = $this->_productOptions['info_buyRequest']['size'];
            if (!is_array($size)) {
                $size = json_decode($size);
            }
            $outputWidth = floatval($size[0]);
            $outputHeight = floatval($size[1]);
        endif;

        $sheetSizeModel = $this->sizeFactory->create()->load($nupSheetSizeId);
        $sheetSizeUnit = $sheetSizeModel->getUnit();
        $nupblidmm = $this->convertUnit($nupBleedMargin, $unit);//convert in to mm
        $outputWidth = $this->convertUnit($outputWidth, $sheetSizeUnit);// convert in to mm
        $outputHeight = $this->convertUnit($outputHeight, $sheetSizeUnit);// convert in to mm
        $pageWidthPx = $this->convertUnit($pageWidth, $sheetSizeUnit);// convert in to mm
        $pageHeightPx = $this->convertUnit($pageHeight, $sheetSizeUnit);//convert in to mm


        $NoOfColumns = floor(($pageWidthPx) / ($outputWidth + ($nupblidmm * 2)));
        $NoOfRows = floor(($pageHeightPx) / ($outputHeight + ($nupblidmm * 2)));

        if (empty($NoOfColumns)) $NoOfColumns = 1;
        if (empty($NoOfRows)) $NoOfRows = 1;

        $TotalLeftMargin = ($pageWidthPx - ($NoOfColumns * ($outputWidth + ($nupblidmm * 2)))) / 2;
        $TotalTopMargin = ($pageHeightPx - ($NoOfRows * ($outputHeight + ($nupblidmm * 2)))) / 2;

        $MMTotalLeftMargin = $TotalLeftMargin;
        $MMTotalTopMargin = $TotalTopMargin;

        $pageWidth = $pageWidthPx;
        $pageHeight = $pageHeightPx;
        $objectWidth = $outputWidth;
        $objectHeight = $outputHeight;
        $mmbleed = $this->convertUnit($bleedMargin, $unit);//convert in to mm

        // initiate FPDI
        $fpdi = new \FPDI();

        $fpdi->setPageUnit("mm");
        $fpdi->SetPrintHeader(false);
        $fpdi->SetPrintFooter(false);
        $orientation = ($pageHeight > $pageWidth) ? 'P' : 'L';
// set auto page breaks
        $fpdi->SetAutoPageBreak(false);
        // add a page
        $page_format = array($pageWidth, $pageHeight);
        
        try {
            foreach ($pdfs as $pdf) {
                //add page
                $fpdi->AddPage($orientation, $page_format, false, false);
                // set the source file
                $fpdi->setSourceFile($pdf);
                // import page 1
                $tplIdx1 = $fpdi->importPage(1);
                $Xpos = 0;//$MMTotalLeftMargin;
                $Ypos = 0;//$MMTotalTopMargin;


                for ($j = 0; $j < $NoOfRows; $j++) {
                    if ($j === 0) {
                        $Ypos = $MMTotalTopMargin; //+ ($nupblidmm * 2);
                        $LineLength = max($MMTotalLeftMargin, $MMTotalTopMargin);
                    } else {
                        $Ypos = $MMTotalTopMargin + ($objectHeight + ($nupblidmm * 2)) * $j;
                        $LineLength = 5;
                    }
                    for ($k = 0; $k < $NoOfColumns; $k++) {
                        if ($k === 0) {
                            $Xpos = $MMTotalLeftMargin; // + ($nupblidmm * 2);
                            $LineLength = max($MMTotalLeftMargin, $MMTotalTopMargin);
                        } else {

                            $Xpos = $MMTotalLeftMargin + ($objectWidth + ($nupblidmm * 2)) * $k;
                            $LineLength = 5;
                        }
                        $fpdi->useTemplate($tplIdx1, $x = $Xpos, $y = $Ypos, $objectWidth, $objectHeight, $adjustPageSize = true);
                        ////add crop marks//////
                        $LineLength = 5;
                        $fpdi->SetFillColor(253, 253, 253, 0);//#FDFDFD
                        $fpdi->cropMark($Xpos + $mmbleed, $Ypos + $mmbleed, $LineLength, $LineLength, 'TL');
                        $fpdi->cropMark($Xpos + $objectWidth - $mmbleed, $Ypos + $mmbleed, $LineLength, $LineLength, 'TR');
                        $fpdi->cropMark($Xpos + $mmbleed, $Ypos + $objectHeight - $mmbleed, $LineLength, $LineLength, 'BL');
                        $fpdi->cropMark($Xpos + $objectWidth - $mmbleed, $Ypos + $objectHeight - $mmbleed, $LineLength, $LineLength, 'BR');


                        /*$slug = 6;
                        $width = $fpdi->getPageWidth();
                        $height = $fpdi->getPageHeight();
                        $outerWidth = $width + 2 * $slug;
                        $outerHeight = $height + 2 * $slug;
                        $barHeight = min($slug - 1, 6);
                        $barWidth = min(9 * $barHeight, ($width - $barHeight * 4)/ 2);
                        $barHeight = max(1, $barWidth / 9);
                        $registrationHeight  = $barHeight / 2;
                        //Registration left
                        $fpdi->registrationMark(
                            $x = -$slug / 2,
                            $y = $width - $height / 2 + 2 * $slug,
                            $registrationHeight,
                            FALSE,
                            array(0, 0, 0),
                            array(255, 255, 255)
                        );

                        //Registration top
                        $fpdi->registrationMark(
                            $x = $width / 2,
                            $y = $outerWidth - $height - $slug / 2,
                            $registrationHeight,
                            FALSE,
                            array(0, 0, 0),
                            array(255, 255, 255)
                        );

                        //Registration right
                        $fpdi->registrationMark(
                            $x = $width + $slug / 2,
                            $y = $width - $height / 2 + 2 * $slug,
                            $registrationHeight,
                            FALSE,
                            array(0, 0, 0),
                            array(255, 255, 255)
                        );

                        //Registration bottom
                        $fpdi->registrationMark(
                            $x = $width / 2,
                            $y = $outerWidth + $slug / 2,
                            $registrationHeight,
                            FALSE,
                            array(0, 0, 0),
                            array(255, 255, 255)
                        );*/

                    }
                }
                // add order number
                $fpdi->SetTextColor(0, 0, 0);
                //$Ypos -1 to make text little up
                //$fpdi->Text(0, $pageHeight - 1, "gggg ".$this->orderIncrementId . '-' . $itemId);
                //$fpdi->writeHTMLCell('', '', 0, $pageHeightPx - 9, $html = $this->orderIncrementId . '-' . $itemId, $border = 0, $ln = 0, $fill = 0, $reseth = true, $align = '', $autopadding = false);
                $fpdi->SetFont('helvetica', 'I', 8);
                $mid_x = $pageWidth / 2;
                $fpdi->Text($mid_x - ($fpdi->GetStringWidth('Order Number : '.$this->orderIncrementId . '-' . $itemId) / 2), $pageHeightPx - 8, $html = 'Order Number : '.$this->orderIncrementId . '-' . $itemId);
            }

            $sku = preg_replace('/[^a-zA-Z0-9_.]/', '', $_item->getSku());

            $impositionPDFFileName = $outputPdfPath . $prefix .'-' . $orderEntityId .'-'. $itemId .'-'. $sku .'-'. round(($_item->getQtyOrdered()), 4) . '.pdf';

            $fpdi->Output($impositionPDFFileName, 'F');
            
        } catch (Exception $exc) {
            echo $exc->getMessage();
            exit;
        }
    }
    /*Imposition End*/

    public function generateLowResolutionPdf($outputPNGFiles, $lowPDF, $sideKey = -1){
        // create new PDF document
        $pdf = new \TCPDF();
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Design N Buy');
        $pdf->SetTitle('Output Preview');
        $pdf->SetSubject('Output Preview');
        $pdf->SetKeywords('Output Preview, PDF');
        $pdf->setRasterizeVectorImages(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        // set auto page breaks
        $pdf->SetAutoPageBreak(false);
        $pdf->setPageUnit("mm");

        if($this->_product->getBaseUnit() != '') {
            //$unit = $this->dnbBaseHelper->getUnit($this->_product->getBaseUnit());
            $unit = $this->_product->getBaseUnit();
        } else {
            if($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId)
            $unit = $this->dnbBaseHelper->getBaseUnit();
            else
            $unit = $this->dnbBaseHelper->getmerchandiseBaseUnit();
        }
        $i = 0;

        foreach($outputPNGFiles as $outputPNG):
            if (($this->_product->getType_id()== 'simple') && ($this->_product->getAttributeSetId()== $this->_customCanvasAttributeSetId)):
                $size = $this->_productOptions['info_buyRequest']['size'];
                $size = json_decode($size);
                $outputWidth = floatval($size[0]);
                $outputHeight = floatval($size[1]);
            endif;
            $configAreas = [];


            if (($this->_product->getIsCustomizable()) && ($this->_product->getAttributeSetId()== $this->_customProductAttributeSetId)):
                $outputWidth = 50;
                $outputHeight = 50;
                if(isset($this->_productOptions['info_buyRequest']) && isset($this->_productOptions['info_buyRequest']['config_area'])){
                    $configAreas = $this->_productOptions['info_buyRequest']['config_area'];
                    if(is_array($configAreas)){
                        $configArea = $configAreas[$i];
                        $area = explode(',', $configArea);
                    } else {
                        $configAreas = explode(',', $configAreas);
                        $configAreas = array_chunk($configAreas, 6);
                        $area = $configAreas[$i];
                    }
                    if(isset($area) && !empty($area)){
                        if (isset($area[4]) || array_key_exists(4, $area)) {
                            $outputWidth = $area[4];
                        }
                        if (isset($area[5]) || array_key_exists(5, $area)) {
                            $outputHeight = $area[5];
                        }
                    }
                }

            endif;

            $width = $this->convertUnit($outputWidth, $unit);
            $height = $this->convertUnit($outputHeight, $unit);
            $orientation = ($height > $width) ? 'P' : 'L';
            $page_format = array($width, $height);
            if($outputPNG != '' & file_exists($this->_sourceImagePath.$outputPNG)):
                // add a page
                $pdf->AddPage($orientation, $page_format, false, false);
                // NOTE: Uncomment the following line to rasterize SVG image using the ImageMagick library.
                //$pdf->setRasterizeVectorImages(true);
                $pdf->Image($file = $this->_sourceImagePath.$outputPNG, $x=0, $y=0, $w = $width, $h = $height, $link='', $align='', $palign='', $border=0, $fitonpage=false);
                //unlink($this->_sourceImagePath.$outputPNG);
            endif;
            $i++;
        endforeach;

        // unlink($sourceImagePath.$cmykSvgFileName);
        $pdf->Output($lowPDF, 'F');

    }
    public function generateCMYKPDF($RGBPDF, $CMYKPDF){
        try{
            $ghost = new Ghostscript(); // the location of 'gs' if your PHP does not execute it correctly by default. To find, enter 'which gs' in Command Line
            $ghost->set_input($RGBPDF);  // your input file
            $ghost->add_option("-dSAFER");
            $ghost->add_option("-dBATCH");
            $ghost->add_option("-dNOPAUSE");
            $ghost->add_option("-dAutoFilterColorImages=true");
            $ghost->add_option("-sProcessColorModel=DeviceCMYK");
            $ghost->add_option("-sColorConversionStrategy=CMYK");
            $ghost->add_option("-sColorConversionStrategyForImages=CMYK");
            $ghost->add_option("-sDEVICE=pdfwrite");
            $ghost->add_option("-q");
            $ghost->add_option("-sOutputFile=".$CMYKPDF);
            $ok = $ghost->export(); // run the 'gs' command
            /*if($ok != 0){
                Mage::log("Ghostscript Message".$ok, null, "dnb.log");
            }*/
        }catch ( Exception $exc ){
            //Mage::log("Ghostscript Message".$exc->getMessage(), null, "dnb.log");
        }
        return;
    }

    public function resizeGeneratedPDF($sourceImagePath, $pdfTempName, $pdfName, $sideKey = -1){
        /*get Side and get configure area and generate output accordingly*/
        //echo "side".$sideKey;

        if($sideKey == -1 ){
            $sideKey = $this->_side;
        }

        if (($this->_product->getType_id()== 'simple') && ($this->_product->getAttributeSetId()== $this->_customCanvasAttributeSetId)):
            $size = $this->_productOptions['info_buyRequest']['size'];
            if(!is_array($size)){
                $size = json_decode($size);
            }
            $outputWidth = floatval($size[0]);
            $outputHeight = floatval($size[1]);
            if($this->_product->getIsPhotobook() == 1 && $sideKey == 0){
                if($this->_product->getCoverExtraWidth() != "" && $this->_product->getCoverExtraHeight() != ""){
                    $outputWidth = $outputWidth + $this->_product->getCoverExtraWidth();
                    $outputHeight = $outputHeight + $this->_product->getCoverExtraHeight();
                }
            }
        endif;

        if (($this->_product->getIsCustomizable()) && ($this->_product->getAttributeSetId()== $this->_customProductAttributeSetId)):
            $outputWidth = 50;
            $outputHeight = 50;
            if(isset($this->_productOptions['info_buyRequest']) && isset($this->_productOptions['info_buyRequest']['config_area'])){
                $configAreas = $this->_productOptions['info_buyRequest']['config_area'];


                if(is_array($configAreas)){
                    $configArea = $configAreas[$sideKey];
                    $area = explode(',', $configArea);
                } else {
                    $configAreas = explode(',', $configAreas);
                    $configAreas = array_chunk($configAreas, 6);
                    $area = $configAreas[$sideKey];
                }
                if(isset($area) && !empty($area)){
                    if (isset($area[4]) || array_key_exists(4, $area)) {
                        $outputWidth = $area[4];
                    }
                    if (isset($area[5]) || array_key_exists(5, $area)) {
                        $outputHeight = $area[5];
                    }
                }
            }
        endif;

        if($this->_product->getBaseUnit() != '') {
            //$unit = $this->dnbBaseHelper->getUnit($this->_product->getBaseUnit());
             $unit = $this->_product->getBaseUnit();
        } else {
            if($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId)
            $unit = $this->dnbBaseHelper->getBaseUnit();
            else
            $unit = $this->dnbBaseHelper->getmerchandiseBaseUnit();
        }
        $width = $this->convertToInch($outputWidth, $unit) * 72;
        $height = $this->convertToInch($outputHeight, $unit) * 72;

        /*$pdfName = explode('.pdf',$pdfFile);
        $pdfNewName = $pdfName[0].'1.pdf';*/

        try{
            $ghost = new Ghostscript(); // the location of 'gs' if your PHP does not execute it correctly by default. To find, enter 'which gs' in Command Line
            $ghost->set_input($sourceImagePath . $pdfTempName);  // your input file
            $ghost->add_option("-dSAFER");
            $ghost->add_option("-dBATCH");
            $ghost->add_option("-dNOPAUSE");
            $ghost->add_option("-dPDFFitPage");
            $ghost->add_option("-dDEVICEWIDTHPOINTS=".$width);
            $ghost->add_option("-dDEVICEHEIGHTPOINTS=".$height);
            $ghost->add_option("-sDEVICE=pdfwrite");
            $ghost->add_option("-q");
            $ghost->add_option("-sOutputFile=".$sourceImagePath . $pdfName);
            $ok = $ghost->export(); // run the 'gs' command
            unlink($sourceImagePath.$pdfTempName);
            /*if($ok != 0){
                Mage::log("Ghostscript Message".$ok, null, "dnb.log");
            }*/
        }catch ( Exception $exc ){
            //Mage::log("Ghostscript Message".$exc->getMessage(), null, "dnb.log");
        }
        return;
    }

    public function convertToInch($value, $unit){
        if($unit == 'px'){
            $value = $value / 72;
            return $value;
        }else if($unit == 'mm'){
            $value = $value / 25.4;
            return $value;
        }else if($unit == 'cm'){
            $value = $value / 2.54;
            return $value;
        }else if($unit == 'm'){
            $value = $value * 39.370078740157474;//($value / 2.54)*100;
            return $value;
        }else if($unit == 'ft'){
            $value = $value *12;
            return $value;
        }else{
            return $value;
        }
    }


    public function convertUnit($value, $unit){// to mm
        if($unit == 'px'){
            $value = ($value*25.4)/96;
            return $value;
        }else if($unit == 'in'){
            $value = $value/0.03937;
            return $value;
        }else if($unit == 'cm'){
            $value = $value * 10;
            return $value;
        }else if($unit == 'm'){
            $value = $value * 10*100;
            return $value;
        }else if($unit == 'ft'){
            $value = $value*304.8006096012192;//($value/0.03937)*12;
            return $value;
        }else{
            return $value;
        }
    }

    public function generateSVG($svgOutputFiles, $orderEntityId='', $item_id='', $nameArray = [], $numberArray = []){
        $mediaPath = $this->outputHelper->getMediaPath();
        $corporateImageDir = $this->dnbBaseHelper->getCustomerImageDir();

        $vectorImagePath = $this->outputHelper->getCartDesignsDir();
        $outputPath = $this->outputHelper->getOutputDir() ."order-".$orderEntityId."-".$item_id. DIRECTORY_SEPARATOR;
        if (!is_dir($outputPath)) {
            mkdir($outputPath, 0777);
        }

        $svgFilesToZip = array();
        $imageFilesToZip = array();

        /*For Name Number Start*/
        $svgNameNumberArray = array();
        $svgNumber = 0;
        /*For Name Number End*/

        foreach ($svgOutputFiles as $svg):
            /*For Name Number Start*/
            $nameNumberFlag = 0;
            /*For Name Number End*/
            $svgFileName = explode('.',$svg);
            if($svgFileName[1]=='svg'):
                if(file_exists($vectorImagePath.$svg)):
                    $svgFilesToZip[] = $svg;
                    $svgfileContents = file_get_contents($vectorImagePath.$svg);
                    $doc = new DOMDocument();
                    $doc->preserveWhiteSpace = False;
                    $doc->loadXML($svgfileContents);
                    foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element):
                        /*For quick edit, issue in when generating the PNG from SVG*/
                        if($element->localName == 'svg'){
                            $element->setAttribute('x',0);
                            $element->setAttribute('y',0);
                        }
                        foreach ($element->getElementsByTagName("*") as $tags):
                            if($tags->localName=='image' && $tags->getAttribute('xlink:href')!=''):
                                if($tags->getAttribute('output-src')){
                                    $outputImage = $tags->getAttribute('output-src');
                                    $tags->setAttribute('xlink:href', $outputImage);
                                }
                                /*To resolve image position issue in Corel Draw and AI Start*/
                                $x = $tags->getAttribute('x');
                                $y = $tags->getAttribute('y');

                                $transform = '';
                                if($tags->getAttribute('transform')){
                                    $transform = $tags->getAttribute('transform')." ";
                                }
                                $transform .= "translate(".$x.",".$y.")";
                                $tags->setAttribute('transform', $transform);

                                $tags->removeAttribute('x');
                                $tags->removeAttribute('y');
                                /*To resolve image position issue in Corel Draw and AI End*/
                                $canvasOverlayImg = $tags->getAttribute('id');
                                if($canvasOverlayImg == 'canvas_overlay_img' && $tags->getAttribute('keepinoutput') != 'true'){
                                    $tags->setAttribute("display",'none');
                                }
                                if(($tags->getAttribute('isAdminUploaded') == 'true' || $tags->getAttribute('isadminuploaded') == 'true') && $tags->parentNode->getAttribute('type') == 'photobox'  && $tags->parentNode->getAttribute('isvdp') != 'true'){
                                    //$tags->parentNode->setAttribute("display", "none");
                                }

                                if($tags->getAttribute('isAdminUploaded') == 'true' || $tags->getAttribute('isadminuploaded') == 'true'){
                                    if($tags->parentNode->parentNode->getAttribute("type") == "photobox" && $tags->parentNode->parentNode->getAttribute('isvdp') != 'true'){
                                        $tags->parentNode->setAttribute("display", "none");
                                        $tags->parentNode->parentNode->setAttribute("display", "none");
                                    }
                                }

                                $imageUrl = $tags->getAttribute('xlink:href');
                                // $name = pathinfo($imageUrl, PATHINFO_FILENAME);
                                $name = pathinfo($imageUrl, PATHINFO_BASENAME);
                                if($name == 'dragImage.svg'){
                                    $tags->setAttribute("svgtype",'dragImage');
                                    $tags->setAttribute("display",'none');
                                    $tags->parentNode->setAttribute("display", "none");
                                    $children = $tags->parentNode->childNodes;
                                    foreach( $children as $child ) {
                                        $child->setAttribute("display",'none');
                                    }
                                }else{
                                    $tags->setAttribute('xlink:href', $name);
                                    /* $collection = Mage::getModel('design/userimage')->getCollection();
                                    $collection->addFieldToFilter('customer_id',$customerId); */
                                    $uploadedImage = explode('media/',$imageUrl);
                                    if(isset($uploadedImage) && !empty($uploadedImage) && array_key_exists(1,$uploadedImage)):
                                        if(($tags->getAttribute('isAdminUploaded') == 'true' || $tags->getAttribute('isadminuploaded') == 'true') && $tags->parentNode->getAttribute('type') != 'photobox'){
                                            /* $adminImageCollection = $this->_adminImageCollectionFactory->create();
                                            $adminImageCollection->addFieldToFilter('image', $name);
                                            $adminImage = $adminImageCollection->getFirstItem();
                                            if($adminImage->getHdImage() != ''){
                                                copy($this->dnbBaseHelper->getAdminImageDir().$adminImage->getHdImage(), $outputPath.$adminImage->getHdImage());
                                            } */
                                        }else{
                                            if($this->_customerId){

                                                $imageCollection = $this->_customerImageCollectionFactory->create();
                                                $imageCollection->addFieldToFilter('customer_id',$this->_customerId);
                                                $imageCollection->addFieldToFilter('image',$name);

                                                foreach($imageCollection as $image){
                                                    if($image->getHdImage() != ''){
                                                        copy($this->dnbBaseHelper->getCustomerImageDir().$image->getHdImage(), $outputPath.$image->getHdImage());
                                                    }
                                                }
                                            }
                                        }
                                        if(file_exists($mediaPath.$uploadedImage[1])){
                                                $this->copyimageRGB($mediaPath.$uploadedImage[1], $outputPath.$name);
                                        }

                                    endif;
                                }

                                /************************   Add QR-Code svg in to main svg *******************************/
                                
                                $imgExtension = pathinfo($tags->getAttribute('xlink:href'), PATHINFO_EXTENSION);
                                if($imgExtension == 'svg'){
                                    $qrCodeName = pathinfo($tags->getAttribute('xlink:href'), PATHINFO_BASENAME);
                                    $qrcodepath = $outputPath.$qrCodeName;

                                    $qrbarcodesvg = file_get_contents($qrcodepath);
                                    $doc2 = new DOMDocument();
                                    $doc2->preserveWhiteSpace = False;
                                    $doc2->loadXML($qrbarcodesvg);
                                    $nodeObjForcode = $doc2->getElementsByTagName('g')->item(0);
                                    
                                    
                                    $g = $doc->createElementNS("http://www.w3.org/2000/svg", "g","");
                                    $g->setAttribute('id',$tags->getAttribute('id'));
                                    
                                    if($tags->getAttribute('width') != "" && $nodeObjForcode->parentNode->getAttribute("width") != ""){
                                        
                                        $qrScale = $tags->getAttribute('width') / $nodeObjForcode->parentNode->getAttribute("width"); 
                                        $qrTransform = $tags->getAttribute('transform').' scale('.$qrScale.')';
                                        $g->setAttribute('transform',$qrTransform);

                                    }    

                                        $newNode = $doc->importNode($nodeObjForcode, true);
                                        //apppend G tag and Newnode import and append again inside g
                                        $tags->parentNode->appendChild($g)->appendChild($newNode);
                                    
                                }
                                
                                /************************   Add QR-Code svg in to main svg *******************************/
                            endif;
                            if($tags->getAttribute("type") == "textarea" || $tags->getAttribute("type") == "advance"){
                                foreach($tags->childNodes as $node){
                                    if($node->nodeName == 'rect'){
                                        $node->setAttribute("display",'none');
                                    }
                                    if($node->nodeName == 'tspan'){
                                        $node->removeAttribute('dx');
                                        $node->setAttribute('x',$node->getAttribute('bx'));
                                    }
                                }
                            }
                            /*For Name Number Start*/
                            if($tags->getAttribute('id') == 'nameText' || $tags->getAttribute('id') == 'numberText'){
                                $nameNumberFlag = 1;
                            }
                            /*For Name Number End*/
                        endforeach;
                    endforeach;
                    $doc->save($outputPath.$svg);
                    
                    /*******************    To Remove QR-Code images from svg ************************/
                    
                    $images = $doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'image'); 
                    for($cnt=0; $cnt < $images->length; $cnt++){
                        $qrcodeExtension = pathinfo($images->item($cnt)->getAttribute("xlink:href"),PATHINFO_EXTENSION);
                        if($qrcodeExtension == 'svg'){
                            $images->item($cnt)->parentNode->removeChild($images->item($cnt));
                            $cnt--;
                        }
                    }
                    $doc->save($outputPath.$svg);

                    /*******************    End To Remove QR-Code images from svg ************************/
                    
                    /*For Name Number Start*/

                    /* make defs always on top when generate PDF otherwise getting issue */
                    //$this->nodeTraverse($outputPath,$svg);

                    if($nameNumberFlag == 1){
                        $svgArray = $this->setName($svg, $vectorImagePath, $outputPath, $nameArray, $numberArray, $svgNumber);
                        if(isset($svgArray) && !empty($svgArray)){
                            foreach($svgArray as $file){
                                $svgNameNumberArray[$svgNumber][] = $file;
                            }
                        } else {
                            $svgNameNumberArray[$svgNumber][] = '';
                        }
                    }
                    $svgNumber++;
                    /*For Name Number End*/

                endif;
            endif;
        endforeach;
        /*For Name Number Start*/
        return $svgNameNumberArray;
        //return array($svgFilesToZip,$imageFilesToZip);
        /*For Name Number End*/
    }
    public function nodeTraverse($outputFiles, $svg)
    {
        if (file_exists($outputFiles . $svg)):
            $svgfileContents = file_get_contents($outputFiles . $svg);
            $doc = new DOMDocument();
            $doc->preserveWhiteSpace = False;
            $doc->loadXML($svgfileContents);
            $m = 0;
                foreach($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element):
                    if ($m == 0) {
                        $nodeOne = $element->getElementsByTagName('defs')->item(0);
                        $nodeTwo = $element->getElementsByTagName('g')->item(0);
                    if (!empty($nodeOne) && !empty($nodeOne)) {
                        $nodeOne->parentNode->insertBefore($nodeTwo, $nodeOne->nextSibling);
                    }
                    }
                endforeach;
            $doc->save($outputFiles . $svg);
        endif;
    }

    public function setName($outputSvgFile, $vectorFilePath, $outputPath, $nameArray, $numberArray, $svgNumber)
    {
        $tempVar = 0;
        $outputSvgFilesArray = array();
        $svgfileContents = file_get_contents($outputPath . $outputSvgFile);
        //print_r($svgfileContents);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = False;
        $doc->loadXML($svgfileContents);
        $svgFileName = explode('.',$outputSvgFile);

        if(count($nameArray) > 0) {
            for($i=0; $i < count($nameArray);$i++){
                foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
                    foreach ($element->getElementsByTagName("*") as $tags) {
                        if(is_array($nameArray) && $tags->getAttribute('id') == 'nameText'){
                            // $tags->nodeValue = $nameArray[$i];
                            $children = $tags->childNodes;
                            if (!empty($children)) {
                                foreach ($children as $child) {
                                    if($nameArray[$i]['d'])$child->setAttribute('d', $nameArray[$i]['d']);
                                    //$child->setAttribute('transform', $nameArray[$i]['transform']);
                                }
                            }
                        }
                    }
                }
                $filename = $svgFileName[0].'_NN'.$i . '.svg';
                $doc->save($outputPath.$filename);
                $number = $numberArray[$i];
                $outputModifiedSvgFile = $this->setNumber($filename, $vectorFilePath, $outputPath, $number);
                $outputSvgFilesArray[] = $outputModifiedSvgFile;
                $tempVar++;
            }
            unlink($outputPath . $outputSvgFile);
            return $outputSvgFilesArray;
        }else{
            if(count($numberArray) > 0 && count($nameArray) == 0){
                for($i=0; $i < count($numberArray);$i++){
                    foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
                        foreach ($element->getElementsByTagName("*") as $tags) {
                            if(is_array($numberArray) && $tags->getAttribute('id') == 'numberText'){
                                $tags->nodeValue = $numberArray[$i];
                            }
                        }
                    }
                    $filename = $svgFileName[0].'_NN'.$i . '.svg';
                    $doc->save($outputPath.$filename);
                    $outputSvgFilesArray[] = $filename;
                    $tempVar++;
                }
                unlink($outputPath . $outputSvgFile);
                return $outputSvgFilesArray;
            }
        }
    }

    public function setNumber($outputSvgFile, $vectorFilePath, $outputPath, $number)
    {
        $svgfileContents = file_get_contents($outputPath . $outputSvgFile);
        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = False;
        $doc->validateOnParse = true;
        $doc->loadXML($svgfileContents);
        foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
            foreach ($element->getElementsByTagName("*") as $tags) {
                //echo $tags->tagName;
                if($number!= '' && $tags->getAttribute('id') == 'numberText'){
                    // $tags->nodeValue = $number;
                    $children = $tags->childNodes;

                    if (!empty($children)) {
                        foreach ($children as $child) {
                            $child->setAttribute('d', $number['d']);
                            //$child->setAttribute('transform', $number['transform']);
                        }
                    }
                }
            }
        }
        $doc->save($outputPath.$outputSvgFile);
        return $outputSvgFile;
    }

    public function zipOutputFiles1($orderEntityId,$itemId) {

        /*$outputFolderPrefix = $this->hotFolderHelper->outputFolderPrefix($this->_product);
        $outputFolderPostfix = $this->hotFolderHelper->outputFolderPostfix($this->_product);
        $outputFolderMiddleName = $this->hotFolderHelper->outputFolderMiddleName($orderEntityId, $itemId, $this->_product);


        $ftpDetails = $this->hotFolderHelper->remoteFTP($this->_product);
        $fullFolderPath = $this->hotFolderHelper->outputFolderLocation($orderEntityId, $this->_product, false);*/

        $outputPath = $this->outputHelper->getOutputDir(). DIRECTORY_SEPARATOR ."order-".$orderEntityId."-".$itemId. DIRECTORY_SEPARATOR;
        $zipPath = $this->outputHelper->getOutputDir();

        $zipFileName = "order-".$orderEntityId."-".$itemId.".zip";
        if($this->_fromAdmin == true){
            $zipFileName = "vdp_order-".$orderEntityId."-".$itemId.".zip";
        }
        $destination = $zipPath.$zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
            foreach (glob($outputPath . '/*') as $file) {
                if ($file !== $destination) {
                    if(strtoupper($this->_outPutFormat) == 'PDF'){
                        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                        if($fileExt != 'svg'){
                            $zip->addFile($file, substr($file, strlen($outputPath)));
                        } else {
                            unlink($file);
                        }
                    } elseif (strtoupper($this->_outPutFormat) == 'SVG'){
                        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                        if($fileExt != 'pdf'){
                            $zip->addFile($file, substr($file, strlen($outputPath)));
                        }
                    } else{
                        $zip->addFile($file, substr($file, strlen($outputPath)));
                    }
                }
            }
            $zip->close();
        }
        //$this->deleteDir($outputPath);
        return;
    }

    public function zipOutputFiles($orderEntityId, $itemId, $productId) {
        $outputPath = $this->outputHelper->getOutputDir() . "order-".$orderEntityId."-".$itemId. DIRECTORY_SEPARATOR;
        //$zipPath = Mage::getBaseDir(). DIRECTORY_SEPARATOR .'media' . DIRECTORY_SEPARATOR .'output'. DIRECTORY_SEPARATOR;

        $outputFolderPrefix = $this->hotFolderHelper->outputFolderPrefix($this->_product);
        $outputFolderPostfix = $this->hotFolderHelper->outputFolderPostfix($this->_product);
        $outputFolderMiddleName = $this->hotFolderHelper->outputFolderMiddleName($orderEntityId, $itemId, $this->_product);

        if($outputFolderPrefix != '' || $outputFolderPostfix != '') {
            $zipFileName = '';

            if($outputFolderPrefix != ''){
                $zipFileName .= $outputFolderPrefix.'_';
            }
            //if($outputFolderMiddleName != ''){
                $zipFileName .= $outputFolderMiddleName.'_';
            //}
            if($outputFolderPostfix != ''){
                $zipFileName .= $outputFolderPostfix;
            }
            $zipFileName .= ".zip";

            //$zipFileName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".zip";

            if($this->_orderInformationFormat == 'csv'){
                $orderFileName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".csv";
            } elseif ($this->_orderInformationFormat == 'xml'){
                $orderFileName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".xml";
            } elseif ($this->_orderInformationFormat == 'txt') {
                $orderFileName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".txt";
            } else {
                $orderFileName = $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".xml";
            }

        } else {
            $zipFileName = "order-".$orderEntityId."-".$itemId.".zip";

            if($this->_orderInformationFormat == 'csv'){
                $orderFileName = "order-".$orderEntityId."-".$itemId.".csv";
            } elseif ($this->_orderInformationFormat == 'xml'){
                $orderFileName = "order-".$orderEntityId."-".$itemId.".xml";
            } elseif ($this->_orderInformationFormat == 'txt') {
                $orderFileName = "order-".$orderEntityId."-".$itemId.".txt";
            } else {
                $orderFileName = "order-".$orderEntityId."-".$itemId.".xml";
            }
        }
        if($this->_fromAdmin == true){
            //|| $outputFolderMiddleName != ''
            if($outputFolderPrefix != '' || $outputFolderPostfix != '') {
                $zipFileName = 'vdp_order-';

                if($outputFolderPrefix != ''){
                    $zipFileName .= $outputFolderPrefix.'_';
                }
                if($outputFolderMiddleName != ''){
                    $zipFileName .= $outputFolderMiddleName.'_';
                }
                if($outputFolderPostfix != ''){
                    $zipFileName .= $outputFolderPostfix;
                }
                $zipFileName .= ".zip";
                //$zipFileName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".zip";
                if($this->_orderInformationFormat == 'csv'){
                    $orderFileName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".csv";
                } elseif ($this->_orderInformationFormat == 'xml'){
                    $orderFileName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".xml";
                } elseif ($this->_orderInformationFormat == 'txt') {
                    $orderFileName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".txt";
                } else {
                    $orderFileName = "vdp_order-" . $outputFolderPrefix . '_' . $outputFolderMiddleName . '_' . $outputFolderPostfix.".xml";
                }
            } else {
                $zipFileName = "vdp_order-".$orderEntityId."-".$itemId.".zip";
                if($this->_orderInformationFormat == 'csv'){
                    $orderFileName = "vdp_order-".$orderEntityId."-".$itemId.".csv";
                } elseif ($this->_orderInformationFormat == 'xml'){
                    $orderFileName = "vdp_order-".$orderEntityId."-".$itemId.".xml";
                } elseif ($this->_orderInformationFormat == 'txt') {
                    $orderFileName = "vdp_order-".$orderEntityId."-".$itemId.".txt";
                } else {
                    $orderFileName = "vdp_order-".$orderEntityId."-".$itemId.".xml";
                }

            }

        }

        /*FTP Connection*/
        $ftpDetails = $this->hotFolderHelper->remoteFTP($this->_product);
        $fullFolderPath = $this->hotFolderHelper->outputFolderLocation($orderEntityId, $this->_product, false);
        $outputDetails = array();
        //$outputDetails['output_path'] = $this->_destinationFolder;
        $outputDetails['zip_name'] =  $zipFileName;
        $outputDetails['order_file_name'] =  $orderFileName;
        $outputDetails['ftp'] =  $ftpDetails;
        $outputDetails['folder_path'] =  $fullFolderPath;
        $outputJSON = json_encode($outputDetails);

        if(isset($this->_productOptions['info_buyRequest']['attachment']['fileName'])){
            if(!empty($this->_productOptions['info_buyRequest']['attachment']['fileName'])){
                $this->_outPutFormat = "attachmentImage";
                if (!is_dir($outputPath)) {
                    mkdir($outputPath, 0777);
                }
                $mediapath = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
                foreach($this->_productOptions['info_buyRequest']['attachment']['fileName'] as $attachedFile)
                {
                    if (file_exists($mediapath.'designnbuy/orderattachment/'.$attachedFile)) {
                        copy($mediapath.'designnbuy/orderattachment/'.$attachedFile,$outputPath.$attachedFile);
                    }
                }
            }
        }
        if($this->_orderInformationFormat == 'csv'){
            $this->generateOrderCSV($orderEntityId, $itemId, $outputPath, $orderFileName, $outputDetails);
        } elseif ($this->_orderInformationFormat == 'xml'){
            $this->generateOrderXML($orderEntityId, $itemId, $outputPath, $orderFileName, $outputDetails);
        } elseif ($this->_orderInformationFormat == 'txt') {
            $this->generateOrderTXT($orderEntityId, $itemId, $outputPath, $orderFileName, $outputDetails);
        } else {
            $this->generateOrderXML($orderEntityId, $itemId, $outputPath, $orderFileName, $outputDetails);
        }

        //$destination = $zipPath.$zipFileName;
        $destination = $this->_destinationFolder . $zipFileName;

        $zip = new \ZipArchive();
        if ($zip->open($destination, \ZIPARCHIVE::CREATE) === true) {
            foreach (glob($outputPath . '/*') as $file) {
                if ($file !== $destination) {
                    if(strtoupper($this->_outPutFormat) == 'PDF'){
                        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                        if($fileExt != 'svg'){
                            $zip->addFile($file, substr($file, strlen($outputPath)));
                        } else {
                            unlink($file);
                        }
                    } elseif (strtoupper($this->_outPutFormat) == 'SVG'){
                        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                        if($fileExt != 'pdf'){
                            $zip->addFile($file, substr($file, strlen($outputPath)));
                        }
                    } elseif ($this->_outPutFormat == 'attachmentImage'){
                        $fileExt = pathinfo($file, PATHINFO_EXTENSION);
                        if($fileExt != 'xml'){
                            $zip->addFile($file, substr($file, strlen($outputPath)));
                        }
                    } else{
                        $zip->addFile($file, substr($file, strlen($outputPath)));
                    }
                }
            }
            $zip->close();
        }
        
        $this->deleteDir($outputPath);

        if(isset($ftpDetails) && !empty($ftpDetails)) {
            /*$ftpObj = new FTPClient();
            $ftpConnection = $ftpObj->connect($ftpDetails['ftp_host'], $ftpDetails['ftp_username'], $ftpDetails['ftp_password']);
            if ($ftpConnection) {
                $folderPathArray = explode(DIRECTORY_SEPARATOR, $fullFolderPath);
                foreach($folderPathArray as $folderName){
                    if($folderName != ''){
                        $ftpObj->makeDir($folderName);
                        $ftpObj->changeDir($folderName);
                    }

                }
                $ftpObj->uploadFile($this->_destinationFolder . $zipFileName, $fullFolderPath . $zipFileName);
            }*/
            $ftpObj = new FTPUploader();
            $ftpConnection = $ftpObj->connect($ftpDetails['ftp_host'], $ftpDetails['ftp_username'], $ftpDetails['ftp_password']);
            if ($ftpConnection) {
                $result = $ftpObj->upload($this->_destinationFolder . $zipFileName, $ftpDetails['ftp_path'] .$fullFolderPath . $zipFileName, '', $ftpDetails['ftp_host'], $ftpDetails['ftp_username'], $ftpDetails['ftp_password']);
                if(is_array($result) && !empty($result)){
                    $message = '';
                    if($result['error'] == 0){
                        $message = "Uploaded to hotfolder";
                    } else {
                        $message = $result['message'];
                    }
                    $log = "Order#".$orderEntityId." Item#".$itemId. 'Message: ' . $message.' Path:'.$ftpDetails['ftp_path'] .$fullFolderPath . $zipFileName;
                    $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/hotfolder.log');
                    $logger = new \Zend\Log\Logger();
                    $logger->addWriter($writer);
                    $logger->info($log);
                }
            }
        }
        
        /*Save output path in order item table.*/
        $this->_item->setOutputFilePath($outputJSON);
        $this->_item->save();

        return;
    }

    public function generateOrderXML($orderId, $itemId, $outputPath, $fileName, $outputDetails)
    {
        $orderids = array();
        $poArray = new \SimpleXMLElement('<?xml version="1.0" encoding="utf-8" ?><order></order>');

        $order = $this->_order;
        if(!$order instanceof \Magento\Sales\Model\Order){
            $order = $this->_orderInterface->loadByIncrementId($orderId);
        }
        // order id
        $orderid = $order->getIncrementId();

        // add child element to the SimpleXML object
        //$pOrder = $poArray->addChild('order');

        // addresses
        $shippingAddress = $order->getShippingAddress();

        $billingAddress = $order->getBillingAddress();
        //
        // Add attributes to the SimpleXML element
        $poArray->addChild('number', $orderid);
        $poArray->addChild('createdAt', $order->getCreatedAt());
        $poArray->addChild('quoteId', $order->getQuoteId());
        $poArray->addChild('taxAmount', $order->getTaxAmount());
        $poArray->addChild('discountAmount', $order->getDiscountAmount());
        $poArray->addChild('shippingInclTax', $order->getShippingInclTax());
        $poArray->addChild('grandTotal', $order->getGrandTotal());

        $customer = $poArray->addChild('customer');
        $customer->addChild('customerId', $order->getCustomerId());
        $customer->addChild('fistName', $order->getCustomerFirstname());
        $customer->addChild('secondName', $order->getCustomerLastname());
        $customer->addChild('company', $order->getCompany());
        $customer->addChild('email', $order->getCustomerEmail());
        $customer->addChild('phone', $order->getPhone());
        $customer->addChild('currency', $order->getOrderCurrencyCode());
        $customer->addChild('shippingAddressId', $order->getShippingAddressId());

        $shipping = $poArray->addChild('shipping');
        $shipping->addChild('firstName', $shippingAddress->getFirstname());
        $shipping->addChild('secondName', $shippingAddress->getLastname());
        $shipping->addChild('company', $shippingAddress->getCompany());
        $shipping->addChild('email', $shippingAddress->getEmail());
        $shipping->addChild('phone', $shippingAddress->getTelephone());
        $shipping->addChild('address1', $shippingAddress->getStreetLine(1));
        $shipping->addChild('address2', $shippingAddress->getStreetLine(2));
        $shipping->addChild('address3', $shippingAddress->getStreetLine(3));
        $shipping->addChild('city', $shippingAddress->getCity());
        $shipping->addChild('region', $shippingAddress->getRegion());
        $shipping->addChild('zip', $shippingAddress->getPostcode());
        $shipping->addChild('country', $shippingAddress->getCountry_id());
        $billing = $poArray->addChild('billing');
        $billing->addChild('address1', $billingAddress->getStreetLine(1));
        $billing->addChild('address2', $billingAddress->getStreetLine(2));
        $billing->addChild('address3', $billingAddress->getStreetLine(3));
        $billing->addChild('city', $billingAddress->getCity());
        $billing->addChild('region', $billingAddress->getRegion());
        $billing->addChild('zip', $billingAddress->getPostcode());
        $billing->addChild('country', $billingAddress->getCountry_id());

        $pItems = $poArray->addChild('products');
        $items = $order->getAllVisibleItems();

        // loop through the order items
        foreach ($items as $item) {
            if($item->getItemId() != $itemId) continue;
            $pItem = $pItems->addChild('product');
            $pItem->addChild('createdAt', $item->getCreatedAt());
            $pItem->addChild('productId', $item->getProductId());
            $pItem->addChild('orderId', $orderid);
            $pItem->addChild('sku', htmlspecialchars($item->getSku()));
            $pItem->addChild('name', htmlspecialchars($item->getName()));
            $pItem->addChild('price', $item->getPrice());
            $pItem->addChild('tax', $item->getTaxAmount());
            $pItem->addChild('discount', $item->getDiscount());
            $pItem->addChild('qty', $item->getQtyOrdered());

            $output = $pItem->addChild('output');
            $outputFilePath = $item->getOutputFilePath();
            //$outputDetails = Mage::helper('core')->jsonDecode($outputFilePath);
            if(!empty($outputDetails)){
                $output->addChild('zip', $outputDetails['zip_name']);
                $output->addChild('locaFolder', $outputDetails['folder_path']);
                if(!empty($outputDetails)){
                    $ftpDetails = $outputDetails['ftp'];
                    if(isset($ftpDetails) && !empty($ftpDetails)){
                        $ftp = $output->addChild('ftp');
                        $ftp->addChild('host', $ftpDetails['ftp_host']);
                        $ftp->addChild('port', $ftpDetails['ftp_port']);
                        $ftp->addChild('username', $ftpDetails['ftp_username']);
                        $ftp->addChild('password', $ftpDetails['ftp_password']);
                        $ftp->addChild('path', $ftpDetails['ftp_path']);
                        $ftp->addChild('connectionTimeout', $ftpDetails['connection_timeout']);
                        $ftp->addChild('passiveFtp', $ftpDetails['passive_ftp']);
                    }
                }
            }
        }

        // add the id to the order ids array
        file_put_contents($outputPath.$fileName, $poArray->asXML());
    }

    public function generateOrderCSV($orderId, $itemId, $outputPath, $fileName, $outputDetails)
    {

        $headers = new \Magento\Framework\DataObject(
            [
                'number' => __('Order ID'),
                'createdAt' => __('Purchase Date'),
                'taxAmount' => __('Tax Amout'),
                'discountAmount' => __('Discount Amount'),
                'shippingInclTax' => __('Shipping Incl Tax'),
                'grandTotal' => __('Grand Total'),
                'customerId' => __('Customer Id'),
                'firstName' => __('First Name'),
                'secondName' => __('Second Name'),
                'company' => __('Company'),
                'email' => __('Email'),
                'phone' => __('Phone'),
                'currency' => __('Currency'),
                'shipping_firstName' => __('Shipping First Name'),
                'shipping_secondName' => __('Shipping Second Name'),
                'shipping_company' => __('Shipping Company'),
                'shipping_email' => __('Shipping Email'),
                'shipping_phone' => __('Shipping Phone'),
                'shipping_address1' => __('Shipping Address1'),
                'shipping_address2' => __('Shipping Address2'),
                'shipping_address3' => __('Shipping Address3'),
                'shipping_city' => __('Shipping City'),
                'shipping_region' => __('Shipping Region'),
                'shipping_zip' => __('Shipping Zip'),
                'shipping_country' => __('Shipping Country'),
                'billing_address1' => __('Billing Address1'),
                'billing_address2' => __('Billing Address2'),
                'billing_address3' => __('Billing Address3'),
                'billing_city' => __('Billing City'),
                'billing_region' => __('Billing Region'),
                'billing_zip' => __('Billing Zip'),
                'billing_country' => __('Billing Country'),
                'product_id' => __('Product Id'),
                'name' => __('Name'),
                'sku' => __('SKU'),
                'price' => __('Price'),
                'tax_amount' => __('Tax Amount'),
                'qty_ordered' => __('Qty'),
                'zip_name' => __('ZIP File Name'),
                'folder_path' => __('Folder Path'),
                'ftp_host' => __('FTP Host'),
                'ftp_port' => __('FTP Port'),
                'ftp_username' => __('FTP Username'),
                'ftp_password' => __('FTP Password'),
                'ftp_path' => __('FTP Path'),
                'connection_timeout' => __('Connection Timeout'),
                'passive_ftp' => __('Passive FTP'),

            ]
        );

        $template = '"{{number}}","{{createdAt}}","{{taxAmount}}","{{discountAmount}}","{{shippingInclTax}}","{{grandTotal}}","{{customerId}}","{{firstName}}","{{secondName}}","{{company}}","{{email}}","{{phone}}","{{currency}}",';
        $template .= '"{{shipping_firstName}}","{{shipping_secondName}}","{{shipping_company}}","{{shipping_email}}","{{shipping_phone}}","{{shipping_address1}}","{{shipping_address2}}","{{shipping_address3}}","{{shipping_city}}","{{shipping_region}}","{{shipping_zip}}","{{shipping_country}}",';
        $template .= '"{{billing_address1}}","{{billing_address2}}","{{billing_address3}}","{{billing_city}}","{{billing_region}}","{{billing_zip}}","{{billing_country}}",';
        $template .= '"{{product_id}}","{{name}}","{{sku}}","{{price}}","{{tax_amount}}","{{discount}}","{{qty_ordered}}",';
        $template .= '"{{zip_name}}","{{folder_path}}","{{ftp_host}}","{{ftp_port}}","{{ftp_username}}","{{ftp_password}}","{{ftp_path}}","{{connection_timeout}}","{{passive_ftp}}"';

        $header = $headers->toString($template);
        $header .= "\n";

        $order = $this->_order;
        if(!$order instanceof \Magento\Sales\Model\Order){
            $order = $this->_orderInterface->loadByIncrementId($orderId);
        }

        $orderid = $order->getIncrementId();
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $orderString = '';
        $orderString .= $order->getIncrementId().','.$order->getCreatedAt().','.$order->getTaxAmount().','.$order->getDiscountAmount().','.$order->getShippingInclTax().','.$order->getGrandTotal().','.$order->getCustomerId().','.$order->getCustomerFirstname().','.$order->getCustomerLastname().','.$order->getCompany().','.$order->getCustomerEmail().','.$order->getPhone().','.$order->getOrderCurrencyCode().','.$shippingAddress->getFirstname().','.$shippingAddress->getLastname().','.$shippingAddress->getCompany().','.$shippingAddress->getCompany().','.$shippingAddress->getEmail().','.$shippingAddress->getTelephone().','.$shippingAddress->getStreetLine(1).','.$shippingAddress->getStreetLine(2).','.$shippingAddress->getStreetLine(3).','.$shippingAddress->getCity().','.$shippingAddress->getRegion().','.$shippingAddress->getCountry_id().','.$billingAddress->getStreetLine(1).','.$billingAddress->getStreetLine(2).','.$billingAddress->getStreetLine(3).','.$billingAddress->getCity().','.$billingAddress->getRegion().','.$billingAddress->getPostcode().','.$billingAddress->getCountry_id();


        $items = $order->getAllVisibleItems();
        /*$outputDetails = [
            'zip_name' => '',
            'folder_path' => '',
            'ftp' => [
                'ftp_host' => '',
                'ftp_port' => '',
                'ftp_username' => '',
                'ftp_password' => '',
                'ftp_path' => '',
                'connection_timeout' => '',
                'passive_ftp' => '',
            ],
        ];*/
        $finalOrder = '';
        // loop through the order items
        foreach ($items as $item) {
            if($item->getItemId() != $itemId) continue;
            $product = '';
            $product .= $item->getProductId().','.$item->getSku().','.$item->getName().','.$item->getPrice().','.$item->getTaxAmount().','.$item->getDiscount().','.$item->getQtyOrdered();

            if(!empty($outputDetails)){
                $product .= $outputDetails['zip_name'].','.$outputDetails['folder_path'];
                if(!empty($outputDetails)){
                    $ftpDetails = $outputDetails['ftp'];
                    if(isset($ftpDetails) && !empty($ftpDetails)){
                        $product .= $ftpDetails['ftp_host'].','.$ftpDetails['ftp_port'].','.$ftpDetails['ftp_username'].','.$ftpDetails['ftp_password'].','.$ftpDetails['ftp_path'].','.$ftpDetails['connection_timeout'].','.$ftpDetails['passive_ftp'];


                    }
                }
            }
            $finalOrder .= $orderString . $product;
            $finalOrder .= "\n";
        }
        $finalCSVString =  $header.$finalOrder;

        // add the id to the order ids array
        file_put_contents($outputPath.$fileName, $finalCSVString);
    }

    public function generateOrderTXT($orderId, $itemId, $outputPath, $fileName, $outputDetails)
    {
        $order = $this->_order;
        $order = $this->_order;
        if(!$order instanceof \Magento\Sales\Model\Order){
            $order = $this->_orderInterface->loadByIncrementId($orderId);
        }

        $orderid = $order->getIncrementId();
        $shippingAddress = $order->getShippingAddress();
        $billingAddress = $order->getBillingAddress();

        $orderString = '';
        $orderString .=
            __('Order ID').':'."\t".$order->getIncrementId()."\r\n".
            __('Purchase Date').':'."\t".$order->getCreatedAt()."\r\n".
            __('Tax Amout').':'."\t".$order->getTaxAmount()."\r\n".
            __('Discount Amount').':'."\t".$order->getDiscountAmount()."\r\n".
            __('Shipping Incl Tax').':'."\t".$order->getShippingInclTax()."\r\n".
            __('Grand Total').':'."\t".$order->getGrandTotal()."\r\n".
            __('Customer Id').':'."\t".$order->getCustomerId()."\r\n".
            __('First Name').':'."\t".$order->getCustomerFirstname()."\r\n".
            __('Second Name').':'."\t".$order->getCustomerLastname()."\r\n".
            __('Company').':'."\t".$order->getCompany()."\r\n".
            __('Email').':'."\t".$order->getCustomerEmail()."\r\n".
            __('Phone').':'."\t".$order->getPhone()."\r\n".
            __('Currency').':'."\t".$order->getOrderCurrencyCode()."\r\n".
            __('Shipping First Name').':'."\t".$shippingAddress->getFirstname()."\r\n".
            __('Shipping Second Name').':'."\t".$shippingAddress->getLastname()."\r\n".
            __('Shipping Company').':'."\t".$shippingAddress->getCompany()."\r\n".
            __('Shipping Email').':'."\t".$shippingAddress->getEmail()."\r\n".
            __('Shipping Phone').':'."\t".$shippingAddress->getTelephone()."\r\n".
            __('Shipping Address1').':'."\t".$shippingAddress->getStreetLine(1)."\r\n".
            __('Shipping Address2').':'."\t".$shippingAddress->getStreetLine(2)."\r\n".
            __('Shipping Address3').':'."\t".$shippingAddress->getStreetLine(3)."\r\n".
            __('Shipping City').':'."\t".$shippingAddress->getCity()."\r\n".
            __('Shipping Region').':'."\t".$shippingAddress->getRegion()."\r\n".
            __('Shipping Zip').':'."\t".$shippingAddress->getPostcode()."\r\n".
            __('Shipping Country').':'."\t".$shippingAddress->getCountry_id()."\r\n".
            __('Billing Address1').':'."\t".$billingAddress->getStreetLine(1)."\r\n".
            __('Billing Address2').':'."\t".$billingAddress->getStreetLine(2)."\r\n".
            __('Billing Address3').':'."\t".$billingAddress->getStreetLine(3)."\r\n".
            __('Billing City').':'."\t".$billingAddress->getCity()."\r\n".
            __('Billing Region').':'."\t".$billingAddress->getRegion()."\r\n".
            __('Billing Zip').':'."\t".$billingAddress->getPostcode()."\r\n".
            __('Billing Country').':'."\t".$billingAddress->getCountry_id()."\r\n";


        $items = $order->getAllVisibleItems();

        $finalOrder = '';
        // loop through the order items
        foreach ($items as $item) {
            if($item->getItemId() != $itemId) continue;
            $product = '';
            $product .=
                __('Product Id').':'."\t".$item->getProductId()."\r\n".
                __('Name').':'."\t".$item->getName()."\r\n".
                __('SKU').':'."\t".$item->getSku()."\r\n".
                __('Price').':'."\t".$item->getPrice()."\r\n".
                __('Tax Amount').':'."\t".$item->getTaxAmount()."\r\n".
                __('Dicount').':'."\t".$item->getDiscount()."\r\n".
                __('Qty').':'."\t".$item->getQtyOrdered()."\r\n";

            if(!empty($outputDetails)){
                $product .=
                    __('ZIP File Name').':'."\t".$outputDetails['zip_name']."\r\n".
                    __('Folder Path').':'."\t".$outputDetails['folder_path'];
                if(!empty($outputDetails)){
                    $ftpDetails = $outputDetails['ftp'];
                    if(isset($ftpDetails) && !empty($ftpDetails)){
                        $product .=
                            __('FTP Host').':'."\t".$ftpDetails['ftp_host']."\r\n".
                            __('FTP Port').':'."\t".$ftpDetails['ftp_port']."\r\n".
                            __('FTP Username').':'."\t".$ftpDetails['ftp_username']."\r\n".
                            __('FTP Password').':'."\t".$ftpDetails['ftp_password']."\r\n".
                            __('FTP Path').':'."\t".$ftpDetails['ftp_path'].PHP_EOL.
                            __('Connection Timeout').':'."\t".$ftpDetails['connection_timeout']."\r\n".
                            __('Passive FTP').':'."\t".$ftpDetails['passive_ftp']."\r\n";
                    }
                }
            }
            $finalOrder .= $orderString . $product;
            $finalOrder .= "\r\n";
        }

        $finalTXTString =  $finalOrder;

        // add the id to the order ids array
        file_put_contents($outputPath.$fileName, $finalTXTString);
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

    public function catalogProductLoadAfter(Varien_Event_Observer $observer)
    {
        // set the additional options on the product
        $action = Mage::app()->getFrontController()->getAction();
        if ($action->getFullActionName() == 'checkout_cart_add')
        {
            // assuming you are posting your custom form values in an array called extra_options...
            //if ($options = $action->getRequest()->getParam('isFromDesigntool'))
            //{
            $product = $observer->getProduct();

            // add to the additional options array
            $additionalOptions = array();
            if ($additionalOption = $product->getCustomOption('additional_options'))
            {
                $additionalOptions = (array) unserialize($additionalOption->getValue());
            }
            $additionalOptions[] = array(
                'label' => 'Designed Product',
                'value' => time(),
            );
            // add the additional options array with the option code additional_options
            $observer->getProduct()
                ->addCustomOption('additional_options', serialize($additionalOptions));
            //}
        }
    }

    public function generateCMYKPDF_TCPDF($svgFileName, $sourceImagePath, $pdfFile, $spotColor = false, $sideKey = -1){
        $this->_usedHexColorCodes = [];
        if($sideKey == -1 ){
            $sideKey = $this->_side;
        }

        if (($this->_product->getType_id()== 'simple') && ($this->_product->getAttributeSetId()== $this->_customCanvasAttributeSetId)):
            $size = $this->_productOptions['info_buyRequest']['size'];
            if(!is_array($size)){
                $size = json_decode($size);
            }
            $outputWidth = floatval($size[0]);
            $outputHeight = floatval($size[1]);
        endif;

        if (($this->_product->getIsCustomizable()) && ($this->_product->getAttributeSetId()== $this->_customProductAttributeSetId)):
            $outputWidth = 50;
            $outputHeight = 50;
            if(isset($this->_productOptions['info_buyRequest']) && isset($this->_productOptions['info_buyRequest']['config_area'])){
                $configAreas = $this->_productOptions['info_buyRequest']['config_area'];


                if(is_array($configAreas)){
                    $configArea = $configAreas[$sideKey];
                    $area = explode(',', $configArea);
                } else {
                    $configAreas = explode(',', $configAreas);
                    $configAreas = array_chunk($configAreas, 6);
                    $area = $configAreas[$sideKey];
                }
                if(isset($area) && !empty($area)){
                    if (isset($area[4]) || array_key_exists(4, $area)) {
                        $outputWidth = $area[4];
                    }
                    if (isset($area[5]) || array_key_exists(5, $area)) {
                        $outputHeight = $area[5];
                    }
                }
            }
        endif;

        if($this->_product->getBaseUnit() != '') {
            $unit = $this->_product->getBaseUnit();
        } else {
           if($this->_product->getAttributeSetId() == $this->_customCanvasAttributeSetId)
            $unit = $this->dnbBaseHelper->getBaseUnit();
            else
            $unit = $this->dnbBaseHelper->getmerchandiseBaseUnit();
        }

        //$width = $this->convertToInch($outputWidth, $unit) * 72;
        //$height = $this->convertToInch($outputHeight, $unit) * 72;

        $width = $this->convertUnit($outputWidth, $unit);
        $height = $this->convertUnit($outputHeight, $unit);
        
        // create new PDF document
        $pdf = new \TCPDF();

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Design N Buy');
        $pdf->SetTitle('RGB to CMYK');
        $pdf->SetSubject('RGB to CMYK');
        $pdf->SetKeywords('RGB to CMYK, PDF, example, test, guide');
        $pdf->setRasterizeVectorImages(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(false);

        // set image scale factor
        //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        /* if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        } */
        $pdf->setPageUnit("mm");
        $orientation = ($height > $width) ? 'P' : 'L';
        $page_format = array($width, $height);
        // add a page
        $pdf->AddPage($orientation, $page_format, false, false);

        // NOTE: Uncomment the following line to rasterize SVG image using the ImageMagick library.
        //$pdf->setRasterizeVectorImages(true);

        $doc = new DomDocument("1.0");
        $doc->loadXML( file_get_contents($sourceImagePath.$svgFileName));
        $cmykSvgFileName = "cmyk_".$svgFileName;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        // change font family with tcpdf font name
        foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
                        
            foreach ($element->getElementsByTagName("*") as $tags) {
                if($tags->getAttribute('font-family') !=''){
                    $fontstyle = $tags->getAttribute('font-style');
                    $fontweight = $tags->getAttribute('font-weight');
                    if($fontstyle == '' || $fontstyle == 'undefined'){
                        $fontstyle = 'normal';
                    }
                    if($fontweight == '' || $fontweight == 'undefined'){
                        $fontweight = 'normal';
                    }
                    $fontfamily = $this->getTCPDFfontName($tags->getAttribute('font-family'),$fontstyle,$fontweight);
                    if($fontfamily)
                    $tags->setAttribute('font-family',$fontfamily);
                }
                if($tags->getAttribute("type") == "textarea" || $tags->getAttribute("type") == "advance" && $tags->nodeName == "g"){
                    $tags->removeAttribute("desc");
                }
            }
        }        
        //========================================
        //If round corner
        if($doc->documentElement->getAttribute('clip-path')){
            /*
            Create parent g element and move all elements into it. And apply clipping to parent g element.
            Because TCPDF do not apply the clipping on the SVG tag.
            */
            $this->modifySVG($doc, $sourceImagePath.$cmykSvgFileName);
        }
        $root = $doc->firstChild;
        $this->traverse( $root );

        $doc->save($sourceImagePath.$cmykSvgFileName);

        if($spotColor){
            $usedColorCodes = array_unique($this->_usedHexColorCodes);
            if(isset($this->_printableColors) && !empty($this->_printableColors)) {
                foreach ($usedColorCodes as $key => $value) {                    
                    if (array_key_exists($value, $this->_printableColors)) {
                        $color = $this->_printableColors[$value];
                        $cmykColor = $color['cmykColorCode'];
                        $pdf->AddSpotColor($color['name'], $cmykColor[0], $cmykColor[1], $cmykColor[2], $cmykColor[3]);
                    }
                }
            }
        }

        $pdf->ImageSVG($file = $sourceImagePath.$cmykSvgFileName, $x=0, $y=0, $w = $width, $h = $height, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        unlink($sourceImagePath.$cmykSvgFileName);
        
        $pdf->Output($pdfFile, 'F');
    }

    public function generateCMYKPDF_TCPDFManual($svgFileName, $sourceImagePath, $pdfFile, $width, $height, $spotColor = false, $sideKey = -1){
        
        $this->_sourceImagePath = $sourceImagePath;
        // create new PDF document
        $pdf = new \TCPDF();

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Design N Buy');
        $pdf->SetTitle('RGB to CMYK');
        $pdf->SetSubject('RGB to CMYK');
        $pdf->SetKeywords('RGB to CMYK, PDF, example, test, guide');
        $pdf->setRasterizeVectorImages(false);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(false);

        // set image scale factor
        //$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        /* if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf->setLanguageArray($l);
        } */
        $pdf->setPageUnit("mm");
        $orientation = ($height > $width) ? 'P' : 'L';
        $page_format = array($width, $height);
        // add a page
        $pdf->AddPage($orientation, $page_format, false, false);

        // NOTE: Uncomment the following line to rasterize SVG image using the ImageMagick library.
        //$pdf->setRasterizeVectorImages(true);

        $doc = new DomDocument("1.0");
        $doc->loadXML( file_get_contents($sourceImagePath.$svgFileName));
        $cmykSvgFileName = "cmyk_".$svgFileName;
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        // change font family with tcpdf font name
        foreach ($doc->getElementsByTagNameNS('http://www.w3.org/2000/svg', 'svg') as $element) {
                        
            foreach ($element->getElementsByTagName("*") as $tags) {
                if($tags->getAttribute('font-family') !=''){
                    $fontstyle = $tags->getAttribute('font-style');
                    $fontweight = $tags->getAttribute('font-weight');
                    if($fontstyle == '' || $fontstyle == 'undefined'){
                        $fontstyle = 'normal';
                    }
                    if($fontweight == '' || $fontweight == 'undefined'){
                        $fontweight = 'normal';
                    }
                    $fontfamily = $this->getTCPDFfontName($tags->getAttribute('font-family'),$fontstyle,$fontweight);
                    if($fontfamily)
                    $tags->setAttribute('font-family',$fontfamily);
                }
            }
        }        
        //========================================
        //If round corner
        if($doc->documentElement->getAttribute('clip-path')){
            /*
            Create parent g element and move all elements into it. And apply clipping to parent g element.
            Because TCPDF do not apply the clipping on the SVG tag.
            */
            $this->modifySVG($doc, $sourceImagePath.$cmykSvgFileName);
        }
        $root = $doc->firstChild;
        $this->traverse( $root );

        $doc->save($sourceImagePath.$cmykSvgFileName);

        if($spotColor){
            $usedColorCodes = array_unique($this->_usedHexColorCodes);
            if(isset($this->_printableColors) && !empty($this->_printableColors)) {
                foreach ($usedColorCodes as $key => $value) {                    
                    if (array_key_exists($value, $this->_printableColors)) {
                        $color = $this->_printableColors[$value];
                        $cmykColor = $color['cmykColorCode'];
                        $pdf->AddSpotColor($color['name'], $cmykColor[0], $cmykColor[1], $cmykColor[2], $cmykColor[3]);
                    }
                }
            }
        }

        $pdf->ImageSVG($file = $sourceImagePath.$cmykSvgFileName, $x=0, $y=0, $w = $width, $h = $height, $link='', $align='', $palign='', $border=0, $fitonpage=false);
        //unlink($sourceImagePath.$cmykSvgFileName);
        
        $pdf->Output($pdfFile, 'F');
    }
    private function getTCPDFfontName($font_name,$font_style,$font_weight)
    {
        if($this->fonts == null)
        {
            $result = $this->_font->getCollection()->getData();
            foreach($result as $row)
            {
                $title = $row['title'];
                $this->fonts[$title]['ttf_tcpdf'] = $row['ttf_tcpdf'];
                $this->fonts[$title]['ttfitalic_tcpdf'] = $row['ttfitalic_tcpdf'];
                $this->fonts[$title]['ttfbold_tcpdf'] = $row['ttfbold_tcpdf'];
                $this->fonts[$title]['ttfbolditalic_tcpdf'] =$row['ttfbolditalic_tcpdf'];
                
            }
        }
        if($font_style == 'italic' && $font_weight == 'bold'){
            $font_key = 'ttfbolditalic_tcpdf';
        }else if($font_style == 'italic' && $font_weight == 'normal'){
            $font_key = 'ttfitalic_tcpdf';
        }else if($font_style == 'normal' && $font_weight == 'bold'){
            $font_key = 'ttfbold_tcpdf';
        }
        else{
            $font_key = 'ttf_tcpdf';
        }
        if(isset($this->fonts[$font_name][$font_key]))
            return $this->fonts[$font_name][$font_key];
        else
            return '';
    }
    function modifySVG($doc, $svgPath){
        if ( $doc->hasChildNodes() ) {
            // Get first child
            $firstChildNode = $doc->firstChild;
            $clipPathAttrValue = $firstChildNode->getAttribute('clip-path');
            // Crete magic element to hold all children in blabla
            $gElement = $doc->createElement('g');
            $gElement->setAttribute('clip-path',$clipPathAttrValue);
            //$firstChildNode->hasChildNodes();
            while($firstChildNode->childNodes->length > 1) {
                if($firstChildNode->firstChild->nodeName != 'defs'){
                    // echo $firstChildNode->firstChild->nodeName;
                    $removedChild = $firstChildNode->removeChild($firstChildNode->firstChild);
                    $gElement->appendChild($removedChild);
                }else{
                    $removedChild = $firstChildNode->removeChild($firstChildNode->firstChild->nextSibling);
                    $gElement->appendChild($removedChild);
                }
            }
            // Append gElement to firstChildNode
            $gElement = $firstChildNode->appendChild($gElement);

        }
        $doc->save($svgPath);
    }

    //For RGB
    public function copyimageRGB($inputImage,$outputImage)
    {
        $formet = shell_exec("identify -format %[colorspace] ".$inputImage);
        if ($formet == "CMYK") {
            $image= $inputImage;
            $i = new \Imagick($image);
            $i->setImageColorspace(\Imagick::COLORSPACE_RGB);
            $i->writeImage($outputImage);
        } else {
            copy($inputImage,$outputImage);
        }
    }


    private function copyimageCMYK($inputImage,$outputImage)
    {
        
        /* $inputExtension = pathinfo($inputImage, PATHINFO_EXTENSION);
        $inputFileName = pathinfo($inputImage, PATHINFO_FILENAME);
        
        
        if ($inputExtension != "" && $inputFileName != "" && $inputExtension == "png") {
            shell_exec("convert ".$inputImage." ".$this->_sourceImagePath.$inputFileName.".tiff");
            $inputImage = $this->_sourceImagePath.$inputFileName.".tiff";
        } */
        $image= $inputImage;
        $i = new \Imagick($image);
        $i->SetColorspace(\Imagick::COLORSPACE_RGB);
        $i->TransformImageColorSpace(\Imagick::COLORSPACE_CMYK);
        $i->writeImage($outputImage);
        //$i->destroy();
    }

    public function traverse( $node, $level=0 ){

        if($node->nodeName != 'defs' && $node->nodeName != 'use' && $node->getAttribute('id') != 'canvas_background' && $node->getAttribute('id') != 'numberText' && $node->getAttribute('id') != 'nameText'){ //Exclude defs and use tags
            $this->handle_node( $node, $level );
        } else {
            if($node->getAttribute('stroke') == "null"){

                $node->removeAttribute('stroke');
            }
        }
        if ( $node->nodeName != 'defs' && $node->getAttribute('id') != 'canvas_background'  ) {
            if($node->hasChildNodes()){
                $children = $node->childNodes;
                foreach( $children as $child ) {
                    if($child->nodeName == 'image'){
                        $imageType = array();
                        $doc = new DomDocument("1.0");

                        $name = pathinfo($child->getAttribute('xlink:href'), PATHINFO_BASENAME);
                        $imageName = pathinfo($child->getAttribute('xlink:href'));
                        $imageType = explode('.',$name);

                        if(array_key_exists('1',$imageType) && $imageType[1] == 'svg' && $imageType[0] != "dragImage"){

                            //@customization=====================================
                            $QrCodeWidth = $child->getAttribute('width');//$QrCodeHeight = $child->getAttribute('height');
                            $QrCodeFullSVGPath = $this->_sourceImagePath.$name;
                            $QrCodePngName = $imageType[0].'.png';
                            $QrCodeFullPNGPath = $this->_sourceImagePath.$QrCodePngName;
                            $QrDpi =  ($QrCodeWidth/84)*90; //@customization 84 is default qrcode svg size and 90 is inkscape default dpi
                            shell_exec("inkscape $QrCodeFullSVGPath -d  $QrDpi -e $QrCodeFullPNGPath --without-gui");
                            $child->setAttribute('xlink:href',$QrCodePngName);
                            //===================================================

                            //For QR code as it is itself a seperate SVG file
                            /* $doc->loadXML( file_get_contents($this->_sourceImagePath.$child->getAttribute('xlink:href')) );
                            $doc->saveXML();
                            $root = $doc->firstChild;
                            $this->traverse( $root );
                            $doc->save($this->_sourceImagePath."cmyk_".$child->getAttribute('xlink:href'));
                            $child->setAttribute('xlink:href','cmyk_'.$child->getAttribute('xlink:href')); */

                        } else {
                            /* Replace file with CMYK File */
                            if($imageName['extension'] && $imageName['extension'] == "png"){
                                $cmykName = $imageName['filename']."_cmyk".'.'."tiff";
                            } else {
                                $cmykName = $imageName['filename']."_cmyk".'.'.$imageName['extension'];
                            }
                            if(substr_count($cmykName, "cmyk") == 1) {
                                if($child->getAttribute('id') != 'canvas_background_img') {
                                if(!file_exists($this->_sourceImagePath.$cmykName)){
                                    $this->copyimageCMYK($this->_sourceImagePath.$name,$this->_sourceImagePath.$cmykName);
                                    if(file_exists($this->_sourceImagePath.$cmykName)){
                                        $child->setAttribute('xlink:href',$cmykName);    
                                    }    
                                }
                                }
                            }
                        }
                    }
                    if ( $child->nodeType == XML_ELEMENT_NODE ) {
                        $this->traverse( $child, $level+1 );
                    }
                }
            }else{
                if($node->nodeName == 'image'){//For QR Code
                    $imageType = array();
                    $doc = new DomDocument("1.0");

                    $name = pathinfo($node->getAttribute('xlink:href'), PATHINFO_BASENAME);
                    $imageName = pathinfo($node->getAttribute('xlink:href'));
                    $imageType = explode('.',$name);
                    if(array_key_exists('1',$imageType) && $imageType[1] == 'svg' && $imageType[0] != "dragImage"){
                        //For QR code as it is itself a seperate SVG file
                        $doc->loadXML( file_get_contents($this->_sourceImagePath.$node->getAttribute('xlink:href')) );
                        $doc->saveXML();
                        $root = $doc->firstChild;
                        $this->traverse( $root );
                        $doc->save($this->_sourceImagePath."cmyk_".$node->getAttribute('xlink:href'));
                        $node->setAttribute('xlink:href','cmyk_'.$node->getAttribute('xlink:href'));

                    } else {
                        /* Replace file with CMYK File */
                        if($imageName['extension'] && $imageName['extension'] == "png"){
                            $cmykName = $imageName['filename']."_cmyk".'.'."tiff";
                        } else {
                            $cmykName = $imageName['filename']."_cmyk".'.'.$imageName['extension'];
                        }
                        if(substr_count($cmykName, "cmyk") == 1) {
                            if($node->getAttribute('id') != 'canvas_background_img') {
                            if(!file_exists($this->_sourceImagePath.$cmykName)){
                                $this->copyimageCMYK($this->_sourceImagePath.$name,$this->_sourceImagePath.$cmykName);
                                if(file_exists($this->_sourceImagePath.$cmykName)){
                                    $node->setAttribute('xlink:href',$cmykName);    
                                }    
                            }
                            }
                        }
                    }
                }

            }

        }
    }

    public function handle_node( $node, $level ) {
        /* for ( $x=0; $x<$level; $x++ ) {

        } */
        if ( $node->nodeType == XML_ELEMENT_NODE ) {
            /*
            If there is no any fill then browser render it as black but TCPDF consider as the fill none. So object display with the fill none.
            So to avoid this fill black as default.
            */
            if(!$node->getAttribute('fill')){
                //$node->setAttribute('fill','#000000');
            }

            if($node->getAttribute('fill') && $node->getAttribute('fill') != 'none'){
                $hexColor = $node->getAttribute('fill');
                if($this->_spotColorOutput){
                    if (array_key_exists($hexColor, $this->_printableColors)) {
                        $color = $this->_printableColors[$hexColor];
                        $cmykColor = $color['name'];
                    } else {
                        $rgbColor = $this->hex2rgb($hexColor);
                        $cmykColor = $this->rgb2cmyk($rgbColor);
                    }
                } else {
                    $rgbColor = $this->hex2rgb($hexColor);
                    $cmykColor = $this->rgb2cmyk($rgbColor);
                }
                
                $node->setAttribute('fill',$cmykColor);
                $this->_usedHexColorCodes[] = $hexColor;
            }
            //If there is a only stroke or stroke-width, set the other value
            if($node->getAttribute('stroke-width') != "" || $node->getAttribute('stroke') != ''){
                if($node->getAttribute('stroke-width') == ""){
                    $node->setAttribute('stroke-width',"1");
                }else if($node->getAttribute('stroke-width') == "0" || $node->getAttribute('stroke-width') == 0){
                    $node->setAttribute('stroke','none');
                }

                if($node->getAttribute('stroke') != 'none'){
                    $hexColor = $node->getAttribute ('stroke');
                    if($this->_spotColorOutput){
                        if (array_key_exists($hexColor, $this->_printableColors)) {
                            $color = $this->_printableColors[$hexColor];
                            $cmykColor = $color['name'];
                        } else {
                            $rgbColor = $this->hex2rgb($hexColor);
                            $cmykColor = $this->rgb2cmyk($rgbColor);
                        }
                    } else {
                        $rgbColor = $this->hex2rgb($hexColor);
                        $cmykColor = $this->rgb2cmyk($rgbColor);
                    }
                    $this->_usedHexColorCodes[] = $hexColor;
                    $node->setAttribute('stroke',$cmykColor);
                }
            }
        }
    }


    /*
    Convert HEX color code to RGB color code
    */
    public function hex2rgb($hex) {
        
        $color = str_replace('#','',$hex);

        if(strlen($color) == 3) { // three letters color code
            $rgb = array('r' => hexdec(substr($color,0,1).substr($color,0,1)),
                'g' => hexdec(substr($color,1,1).substr($color,1,1)),
                'b' => hexdec(substr($color,2,1).substr($color,2,1)));
        }else{ // six letters color code
            $rgb = array('r' => hexdec(substr($color,0,2)),
                'g' => hexdec(substr($color,2,2)),
                'b' => hexdec(substr($color,4,2)));
        }
        return $rgb;
    }

    /*
    Convert RGB color code to CMYK color code
    */
    public function rgb2cmyk($var1,$g=0,$b=0) {
        if (is_array($var1)) {
            $r = $var1['r'];
            $g = $var1['g'];
            $b = $var1['b'];
        } else {
            $r=$var1;
        }
        $c = 0; $m = 0; $y = 0; $k = 0;

        $r = floatval($r)/255.0; $g = floatval($g)/255.0; $b = floatval($b)/255.0;

        $k = 1 - max( $r , max( $g , $b ));
        if( $k==1 )
            $c = $m = $y = 0;
        else
        {
            $c = round(((1-$r-$k)/(1-$k))*100);
            $m = round(((1-$g-$k)/(1-$k))*100);
            $y = round(((1-$b-$k)/(1-$k))*100);
        }

        $k = round($k*100);
        $cmyk = [$c,$m,$y,$k];

        return "cmyk(".implode(',',$cmyk).")";



        /*$cyan    = 255 - $r;
        $magenta = 255 - $g;
        $yellow  = 255 - $b;
        $black  = min($cyan, $magenta, $yellow);
        if($r == 0 && $g == 0 && $b == 0){
            $cyan    = @(($cyan    - $black) / (255 - $black)) * 255;
            $magenta = @(($magenta - $black) / (255 - $black)) * 255;
            $yellow  = @(($yellow  - $black) / (255 - $black)) * 255;
        }

        
        $cmyk = array(
            'c' => ($cyan / 255)*100,
            'm' => ($magenta / 255)*100,
            'y' => ($yellow / 255)*100,
            'k' => ($black / 255)*100);
        return "cmyk(".implode(',',$cmyk).")";*/
    }

}
