<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Controller\Preview;
//use TCPDF_TCPDF;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Request\InvalidRequestException;
/**
 * Background home page view
 */
class Pdf extends \Magento\Framework\App\Action\Action implements CsrfAwareActionInterface
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Base Helper
     *
     * @var \Designnbuy\Base\Helper\Base
     */
    protected $_dnbBaseHelper;

    /**
     * Output Helper
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Base\Helper\Output $outputHelper
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_dnbBaseHelper = $dnbBaseHelper;
        $this->_outputHelper = $outputHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {   
        
        $timeStamp = $this->getRequest()->getParam('currentTime', false);
        $designDir = $this->_outputHelper->getDnbTempDir($timeStamp);
        $size = $this->getRequest()->getParam('productSize');
        $unit = 'in';
        $request = $this->getRequest()->getParams();
        /*$cnt = 0;
        $designPngNames = array();
        if(isset($request['png_data']) && !empty($request['png_data'])) {
            $pngUrls = $request['png_data'];
            foreach ($pngUrls as $pngUrl) {
                //$pngUrl = $request['png_url'];
                $png = base64_encode(file_get_contents($pngUrl));
                $pngImageName = "design_" . $timeStamp . '_' . $cnt . '.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
            }

        }*/


        
        $size = json_decode($size);
        $width = floatval($size[0]);
        $height = floatval($size[1]);
        $pageW = $this->_outputHelper->convertUnit($width, $unit);
        $pageH = $this->_outputHelper->convertUnit($height, $unit);
        $pageSize = array($pageW,$pageH);

        $waterMarkPath = $this->_dnbBaseHelper->getDesignNBuyDir().'watermark/';
        $waterMark = $this->_dnbBaseHelper->getWaterMarkImage();
        $waterMarkImage = '';
        if($this->_dnbBaseHelper->isWatermarkEnabled() && $waterMark != ''){
            $waterMarkImage = $waterMarkPath . $waterMark;
            $imageSize = getimagesize($waterMarkImage);
            $imageSizeRatio = $imageSize[0]/$imageSize[1];
            $imageNewWidth = min($pageSize)/1.5;
            $imageNewHeight = $imageNewWidth/$imageSizeRatio;
        }

        $pdf = new \TCPDF();

        $pdf->setPageUnit("mm");
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->setMargins(0, 0, 0, 0);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetAutoPageBreak(false, 0);

        $pngData = array();
        $pngData = $this->getRequest()->getParam('png_data');
        if(!empty($pngData)){
        //$pngData = $designPngNames;
        $orientation = ($pageH > $pageW) ? 'P' : 'L';


        foreach($pngData as $png){
            $pdf->AddPage($orientation, $pageSize);
            $pdf->Image($file = $png, $x=0, $y=0, $w=$pageW, $h=$pageH, $type='PNG', $link='', $align='', $resize=true, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=true, $alt=false, $altimgs=array());
            if($this->_dnbBaseHelper->isWatermarkEnabled()){
                if($this->_dnbBaseHelper->getWaterMarkType() == 'image' && $this->_dnbBaseHelper->getWaterMarkImage() != ''){
                    $pdf->StartTransform();
                    $pdf->setAlpha(0.5);
                    $pdf->Rotate($angle = 45,$x=$pageW/2, $y=$pageH/2);
                    $pdf->Image($file = $waterMarkImage, $x=($pageW-$imageNewWidth)/2, $y=($pageH-$imageNewHeight)/2, $w=$imageNewWidth, $h=$imageNewHeight, $type='PNG', $link='', $align='', $resize=true, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=true, $alt=false, $altimgs=array());
                    $pdf->setAlpha(1);
                    $pdf->StopTransform();
                } else if($this->_dnbBaseHelper->getWaterMarkType() == 'text' && $this->_dnbBaseHelper->getWaterMarkText() != ''){
                    $pdf->SetAlpha(0.5);
                    $pdf->StartTransform();
                    //$pdf->Rotate(45, $pageW / 2, $pageH / 2);
                    $pdf->SetFont("courier", "", 20);
                    $pdf->SetTextColor(255,0,0);
                    $text_width = $pdf->GetStringWidth($this->_dnbBaseHelper->getWaterMarkText());
                    $pdf->Text(($pageW -  $text_width) / 2, ($pageH ) / 2, $this->_dnbBaseHelper->getWaterMarkText());
                    $pdf->StopTransform();
                    $pdf->SetAlpha(1);
                }
            }
        }

        $outputImagePath = $this->_outputHelper->getDnbTempDir($timeStamp);
        $this->_outputHelper->removeTempDirectory($outputImagePath);
        $pdfData = $pdf->Output("preview.pdf", 'D');
        die;
        } else {
            echo "You have just allowed Pop-up for this domain. So, Please try again."; die;
        }
    }
    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

}
