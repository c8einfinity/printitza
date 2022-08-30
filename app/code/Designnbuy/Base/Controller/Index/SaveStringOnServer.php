<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Index;
use Magento\Framework\Controller\ResultFactory;
/**
 * SaveStringOnServer Action
 */
class SaveStringOnServer extends \Magento\Framework\App\Action\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->_outputHelper = $outputHelper;
        $this->jsonHelper = $jsonHelper;
    }
    /**
     * SaveStringOnServer action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        $designDir = $this->_outputHelper->getCartDesignsDir();
        $timeStamp = time();
        $outPutPath = $this->_outputHelper->getDnbTempDir($timeStamp);
        $result = [];
        /*save svg image on server*/
        $cnt = 0;
        $pngImages = array();
        $designSvgNames = array();

        $configArea = [];
        if(array_key_exists('productImageSvg',$request) && isset($request['productImageSvg']) && !empty($request['productImageSvg'])) {
            if(array_key_exists('configarea',$request) && isset($request['configarea']) && !empty($request['configarea'])) {
                $configArea = json_decode(stripslashes($request['configarea']), true);
            }
        }

        if(isset($request['svg']) && !empty($request['svg'])){
            $svgStr = $request['svg'];
            foreach($svgStr as $svg){
                $svgImageName = "design_".$timeStamp.'_'.$cnt.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $designDir . $svgImageName;
                $this->_outputHelper->saveSvg($svgImagePath, $svg);
                /*Generate png image side from svg */
                if(isset($request['productImageSvg']) && !empty($request['productImageSvg'])) {
                    $designPngName = "svg_".$timeStamp.'_'.$cnt.'.png';
                    $this->_outputHelper->customizeSVG($outPutPath, $svgImageName, urldecode($svg), $timeStamp, '');
                    $this->_outputHelper->convertSVGToPNG($outPutPath, $svgImageName, $designPngName);
                    $pngImages[$cnt]['design'] = $outPutPath.$designPngName;

                    $productPngName = "product_".$timeStamp.'_'.$cnt.'.png';
                    $productSvgName = "product_".$timeStamp.'_'.$cnt.'.svg';
                    $this->_outputHelper->customizeSVG($outPutPath, $productSvgName, urldecode($request['productImageSvg']), $timeStamp, '', 'img_'.$cnt);
                    $this->_outputHelper->convertSVGToPNG($outPutPath, $productSvgName, $productPngName);
                    $pngImages[$cnt]['product'] = $outPutPath.$productPngName;
                    $designName = "design_".$timeStamp.'_'.$cnt.'.png';
                    $result['png'][$cnt] = $designName;
                    $this->_outputHelper->mergeDesignProductImages($outPutPath.$designPngName, $outPutPath.$productPngName, $designDir.$designName, $configArea[$cnt]);
                }
                $cnt++;
            }
        }

        $result['svg'] = $designSvgNames;

        /*save png base64 data on server*/
        $cnt = 0;
        $designPngNames = array();
        if(isset($request['png_data']) && !empty($request['png_data'])) {
            $pngData = $request['png_data'];
            foreach ($pngData as $png) {
                $pngImageName = "design_" . $timeStamp . '_' . $cnt . '.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
                $cnt++;
            }
            $result['png'] = $designPngNames;
        }

        /*save png url on server*/
        $cnt = 0;
        $designPngNames = array();
        if(isset($request['png_url']) && !empty($request['png_url'])) {
            $pngUrls = $request['png_url'];
            foreach ($pngUrls as $pngUrl) {
                //$pngUrl = $request['png_url'];
                $dt = explode($this->_outputHelper->getDnbTempPath(),$pngUrl);
                $png_dir_path = $this->_outputHelper->getMediaPath().$this->_outputHelper->getDnbTempPath().$dt[1];
                //echo "<pre>"; print_r($png); exit;
		if (file_exists($png_dir_path)) {
		$png = base64_encode(file_get_contents($png_dir_path));
                $pngImageName = "design_" . $timeStamp . '_' . $cnt . '.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;
		$this->_outputHelper->saveBase64($pngImagePath, $png);
		}
            }
            $result['png'] = $designPngNames;
        }
        /** @var \Magento\Framework\Json\Helper\Data $helper */
        $helper = $this->jsonHelper;

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
