<?php

namespace Designnbuy\Base\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

class SetVDPQuickEditParams implements ObserverInterface
{

    protected $_request;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $outputHelper;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */
    private $dnbBaseHelper;
    

    /**
     * @param RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper
    ){
        $this->_request = $context->getRequest();
        $this->outputHelper = $outputHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $request = $this->_request->getParams();

        $timeStamp = time();
        $cartDir = $this->outputHelper->getCartDesignsDir();
        if(isset($request['nameNumber']) && !empty($request['nameNumber']) && $request['nameNumber'] != 'undefined'){
            $nameNumber = $request['nameNumber'];

            if(isset($nameNumber['data']) && !empty($nameNumber['data'])){
                $nameNumberFileName = "namenumber_".$timeStamp.'.json';
                $fp = fopen($cartDir.$nameNumberFileName, 'w');
                fwrite($fp, $nameNumber['data']);
                $request['nameNumber']['data'] = $nameNumberFileName;
            }

        }

        if(isset($request['csvfiledata']) && $request['csvfiledata'] !== '""' && $request['csvfiledata'] != 'undefined' && $request['csvheaderdata']){
            $svgData = $request['csvheaderdata'];
            $svgFileName = "svg_".$timeStamp.'.json';
            $fp = fopen($cartDir.$svgFileName, 'w');
            fwrite($fp, $svgData);
            $request['svg_file'] = $svgFileName;
            $request['csvheaderdata'] = '';

            $vdpData = $request['csvfiledata'];
            $vdpData = str_replace("*||*","&#38;",$vdpData);
            $vdpFileName = "vdp_".$timeStamp.'.json';
            $fp = fopen($cartDir.$vdpFileName, 'w');
            fwrite($fp, $vdpData);
            $request['vdp_file'] = $vdpFileName;
            $request['csvfiledata'] = '';
        }else {
            $request['csvheaderdata'] = '';
            $request['csvfiledata'] = '';
        }
        $this->dnbBaseHelper->getCustomCanvasAttributeSetId();

        $pngUrls = array();

        /*save svg image on server*/
        $cnt = 0;
        $designSvgNames = array();
        
        if(isset($request['svgTextArea']) && !empty($request['svgTextArea'])){
            $svgStr = $request['svgTextArea'];
            foreach($svgStr as $svg){
                $svgImageName = "design_".$timeStamp.'_'.$cnt.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $cartDir . $svgImageName;
                $this->outputHelper->saveSvg($svgImagePath, $svg);
                if(!isset($request['previewSvg'])){
                    $data['svg'] = $svg;
                    $data['side'] = $cnt;
                    $data['current_time'] = $timeStamp;
                    $pngUrls[] = $this->outputHelper->generatePng($data);
                }
                $cnt++;
            }
        }

        $cnt = 0;
        if(isset($request['previewSvg']) && !empty($request['previewSvg'])){
            $svgStr = $request['previewSvg'];
            foreach($svgStr as $svg){
                $data['svg'] = $svg;
                $data['current_time'] = $timeStamp.$cnt;
                $pngUrls[] = $this->outputHelper->generatePng($data);
                $cnt++;
            }
        }


        $cnt = 0;
        $designPngNames = array();
        if(isset($pngUrls) && !empty($pngUrls)) {
            //$pngUrls = $request['png_url'];
            foreach ($pngUrls as $pngUrl) {
                //$pngUrl = $request['png_url'];
                $dt = explode($this->outputHelper->getDnbTempPath(),$pngUrl);
                $png_dir_path = $this->outputHelper->getMediaPath().$this->outputHelper->getDnbTempPath().$dt[1];
                $png = base64_encode(file_get_contents($png_dir_path));
                $pngImageName = "design_" . $timeStamp . '_' . $cnt . '.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $cartDir . $pngImageName;
                $this->outputHelper->saveBase64($pngImagePath, $png);
                $cnt++;
            }

        }
        
        if(isset($designSvgNames) && !empty($designSvgNames)){
            $request['svg'] = implode(',', $designSvgNames);
        }

        if(isset($designPngNames) && !empty($designPngNames)){
            $request['png'] = implode(',', $designPngNames);
        }

        $request['svgTextArea']='';
        $request['previewSvg']='';

        $this->_request->setParams($request);
    }
}