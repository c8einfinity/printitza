<?php

namespace Designnbuy\Template\Controller\Adminhtml\Template;

use Designnbuy\Template\Model\Template;

/**
 * Save Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class SaveTemplate extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;


    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $storeId = $this->getRequest()->getParam('store', 0);
        $store = $this->getStoreManager()->getStore($storeId);
        $this->getStoreManager()->setCurrentStore($store->getCode());

        $redirectBack = $this->getRequest()->getParam('back', false);
        $templateId = $this->getRequest()->getParam('template_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();

        $request = $this->getRequest()->getParams();
        $designDir = $this->_outputHelper->getTemplateDesignsDir();
        $timeStamp = time();
        /*save svg image on server*/
        $cnt = 0;
        $designSvgNames = array();
        if(isset($request['save_str']) && !empty($request['save_str'])){
            $svgStr = $request['save_str'];
            foreach($svgStr as $svg){                
                $svgImageName = "template_".$templateId."_".$cnt."_".$storeId.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $designDir . $svgImageName;
                $this->_outputHelper->saveSvg($svgImagePath, $svg);
                $cnt++;
            }
        }

        /*save png base64 data on server*/
        $cnt = 0;
        $designPngNames = array();
        if(isset($request['png_data']) && !empty($request['png_data'])) {
            $pngData = $request['png_data'];
            foreach ($pngData as $png) {
                $pngImageName = "template_".$templateId."_".$cnt."_".$storeId.'.png';
                $designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
                $cnt++;
            }
        }

        /*save png url on server*/
        $cnt = 0;
        $designPngNames = array();
        $pngImageName = '';
        if(isset($request['png_url']) && !empty($request['png_url'])) {
            $pngUrls = $request['png_url'];
            foreach ($pngUrls as $pngUrl) {
                //$pngUrl = $request['png_url'];
                $dt = explode($this->_outputHelper->getDnbTempPath(),$pngUrl);
                $png_dir_path = $this->_outputHelper->getMediaPath().$this->_outputHelper->getDnbTempPath().$dt[1];
                $png = base64_encode(file_get_contents($png_dir_path));
                $pngImageName = "template_".$templateId."_".$storeId.'.png';
                //$designPngNames[$cnt] = $pngImageName;
                $pngImagePath = $designDir . $pngImageName;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
            }

        }
        /** @var \Magento\Framework\Json\Helper\Data $helper */
        $helper = $this->jsonHelper;
        /*$result = [];
        $result['svg'] = $designSvgNames;
        $result['image'] = '/svg/'.$pngImageName;*/
        
        $BgId = $this->getRequest()->getParam('BgId');
        $backgroundData = $helper->jsonEncode($BgId);

        $templateModel = $this->_templateFactory->create();

        $typeId = $this->getRequest()->getParam('type');

        if (!$templateId && $typeId) {
            $templateModel->setTypeId($typeId);
        }
        if($templateModel->getSvg() == ''){
            $templateModel->setStoreId(0);
            $templateModel->load($templateId);
        } else {
            $templateModel->setStoreId($storeId);
            $templateModel->load($templateId);
        }
        //$templateModel->setStoreId($storeId);
        //$templateModel->load($templateId);

        $relatedProducts = $templateModel->getRelatedProducts();

        $linksData = [];

        foreach ($relatedProducts as $item) {
            $linksData[$item->getId()] = [
                'position' => $item->getPosition()
            ];
        }
        
        /*if (!is_null($linksData)) {
            $links['product'] = $linksData;
            $templateModel->setProductsData($links);
        }
        $templateModel->setImage($pngImageName);
        $designSvgs = implode(',',$designSvgNames);
        $templateModel->setSvg($designSvgs);
        $templateModel->setBackgrounds($backgroundData);
        $templateModel->save();
        $templateModel->setAttributeSetId($templateModel->getDefaultAttributeSetId());
        $templateModel->save();*/
        $templateData = [];
        if (!is_null($linksData)) {
            $templateData['product'] = $linksData;
        }
        $templateData['image'] = $pngImageName;
        $designSvgs = implode(',',$designSvgNames);

        $templateData['svg'] = $designSvgs;
        $templateData['backgrounds'] = $backgroundData;
        $templateData['attribute_set_id'] = $templateModel->getDefaultAttributeSetId();
        $templateModel->addData($templateData);
        $templateModel->save();
    }

    /**
     * @return StoreManagerInterface
     * @deprecated
     */
    private function getStoreManager()
    {
        if (null === $this->storeManager) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Store\Model\StoreManagerInterface');
        }
        return $this->storeManager;
    }
}
