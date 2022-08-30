<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Customer\Controller\Design;


use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
/**
 * Background home page view
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * Design factory
     *
     * @var DesignFactory
     */
    protected $_designFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Output $outputHelper
     * @param \Designnbuy\Customer\Model\DesignFactory $designFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        Session $customerSession,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->_outputHelper = $outputHelper;
        $this->_designFactory = $designFactory;
        $this->jsonHelper = $jsonHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();

        $timeStamp = time();
        $designDir = $this->_outputHelper->getCustomerDesignsDir();
        $designUrl = $this->_outputHelper->getCustomerDesignsUrl();

        /*save svg image on server*/
        $cnt = 0;
        $designSvgNames = array();
        if(isset($request['save_str']) && !empty($request['save_str'])){
            $svgStr = $request['save_str'];
            foreach($svgStr as $svg){
                $svgImageName = "design_".$timeStamp.'_'.$cnt.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $designDir . $svgImageName;
                $this->_outputHelper->saveSvg($svgImagePath, $svg);
                $cnt++;
            }
        }

        /*save png image on server*/
        $cnt = 0;
        $designPngNames = array();
        $shareImageUrl = '';
        if(isset($request['png_data']) && !empty($request['png_data'])) {
            $pngData = $request['png_data'];
            foreach ($pngData as $png) {
                $pngImageName = "design_" . $timeStamp . '_' . $cnt . '.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;

                if($cnt == 0){
                    $shareImageUrl = $designUrl.$pngImageName;
                }
                if(isset($request) && !empty($request['toolType']) && $request['toolType'] == 'web2print'){
                    /*get base64 from temp folder of image and then save it into designs folder for web2print tool*/
                    $png = $this->_outputHelper->base64_encode_image($png, '');
                }
                $this->_outputHelper->saveBase64($pngImagePath, $png);
                $cnt++;
            }
        }
        $data = [];
        $data['svg'] = implode(',', $designSvgNames);
        $data['png'] = implode(',', $designPngNames);
        $data['design_name'] = utf8_decode(trim($this->getRequest()->getParam('design_name')));
        if($this->customerSession->isLoggedIn()) {
            $data['customer_id'] = $this->customerSession->getCustomerId();
            $response['status'] = 'true';
        }
        $data['customer_id'] = $this->getRequest()->getParam('cust_id');
        $data['product_id'] = $this->getRequest()->getParam('product_id');
        $data['tool_type'] = $this->getRequest()->getParam('toolType');
        $data['design_status'] = 1;

        $options = [];
        //$options['color_id'] = $this->getRequest()->getParam('color_id');
        
        $color_id = $this->getRequest()->getParam('color_id');
        if($color_id){
            $options['color_id'] = $color_id;
        }
        /** @var \Magento\Framework\Json\Helper\Data $helper */
        $helper = $this->jsonHelper;

        $designData = $this->getRequest()->getParam('design_data');
        if(isset($designData) && !empty($designData)){
            $options['design_data'] = $helper->jsonDecode($designData);
        }

        $nameNumber = $this->getRequest()->getParam('nameNumber');
        if(isset($nameNumber) && !empty($nameNumber)){

            if(isset($nameNumber['data']) && !empty($nameNumber['data'])){
                $nameNumberData = $nameNumber['data'];

                $nameNumberFileName = "namenumber_".$timeStamp.'.json';
                $fp = fopen($designDir.$nameNumberFileName, 'w');
                fwrite($fp, $nameNumberData);
                $nameNumber['data'] = $nameNumberFileName;
            }

            $options['nameNumber'] = $nameNumber;
        }

        $size = $this->getRequest()->getParam('size');
        if($size){
            $options['size'] = $size;
        }

        $no_of_side = $this->getRequest()->getParam('no_of_side');
        if($no_of_side){
            $options['no_of_side'] = $no_of_side;
        }

        $qty = $this->getRequest()->getParam('qty');
        if($qty){
            $options['qty'] = $qty;
        }
        $customOption = [];
        $customOptionData = $this->getRequest()->getParam('customOptionData');
        parse_str($customOptionData, $customOption);

        if(isset($customOption) && !empty($customOption['options'])){
            $options['customOptions'] = $customOption['options'];
        }

        $BgId = $this->getRequest()->getParam('BgId');
        if($BgId){
            $options['BgId'] = $BgId;
        }


        /*VDP Start*/
        if(isset($request['csvfiledata']) && isset($request['csvheaderdata'])){
            $svgData = $request['csvheaderdata'];
            $svgFileName = "svg_".$timeStamp.'.json';
            $fp = fopen($designDir.$svgFileName, 'w');
            fwrite($fp, $svgData);
            $vdpData = $request['csvfiledata'];
            $vdpData = str_replace("*||*","&#38;",$vdpData);
            $vdpFileName = "vdp_".$timeStamp.'.json';
            $fp = fopen($designDir.$vdpFileName, 'w');
            fwrite($fp, $vdpData);
            $options['svg_file'] = $svgFileName;
            $options['vdp_file'] = $vdpFileName;

        }
        /*VDP End*/

        $data['options'] = $helper->jsonEncode($options);
        
        $designFactory = $this->_designFactory->create();
        if ($this->getRequest()->getParam('design_id')) {
            $designFactory->load($this->getRequest()->getParam('design_id'));
        }
        $designFactory->addData($data);
        $designFactory->save();

        $result = [];
        $result['design_id'] = $designFactory->getDesignId();
        $result['design_name'] = $designFactory->getDesignName();
        $result['design_image'] = $shareImageUrl;

        if(isset($request) && !empty($request['toolType'])){
            $shareUrl = '';
            if($request['toolType'] == 'producttool'){
                $shareUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('merchandise/index/index', ['id' => $designFactory->getProductId(), 'design_id' => $designFactory->getDesignId()]);
            }

            if($request['toolType'] == 'web2print'){
                $shareUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('canvas/index/index', ['id' => $designFactory->getProductId(), 'design_id' => $designFactory->getDesignId()]);
            }
            $result['design_url'] = $shareUrl;
        }



        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}