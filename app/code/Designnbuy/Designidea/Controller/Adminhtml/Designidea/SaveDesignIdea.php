<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

use Designnbuy\Template\Model\Template;

/**
 * Save Template action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class SaveDesignIdea extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
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
        $designideaId = $this->getRequest()->getParam('designidea_id');
        $productId = $this->getRequest()->getParam('product_id');

        $request = $this->getRequest()->getParams();
        $designDir = $this->_outputHelper->getDesignIdeaDesignsDir();
        $timeStamp = time();
        /*save svg image on server*/
        $cnt = 0;
        $designSvgNames = array();

        if(isset($request['save_str']) && !empty($request['save_str'])){
            $svgStr = $request['save_str'];
            foreach($svgStr as $svg){
                $svgImageName = "designidea_".$designideaId."_".$cnt."_".$storeId.'.svg';
                $designSvgNames[$cnt] = $svgImageName;
                $svgImagePath = $designDir . $svgImageName;
                $this->_outputHelper->saveSvg($svgImagePath, $svg);
                $cnt++;
            }
        }

        /*save png base64 data on server*/
        $cnt = 0;
        $designPngNames = array();
        $previewImage = '';
        $firstSidePreviewImage = '';
        if(isset($request['png_data']) && !empty($request['png_data'])) {
            $pngData = $request['png_data'];
            foreach ($pngData as $png) {
                $previewImage = "designidea_preview_".$designideaId."_".$cnt."_".$storeId.'.png';
                if($cnt == 0){
                    $firstSidePreviewImage = $previewImage;
                }
                $designPngNames[$cnt] = $previewImage;
                $pngImagePath = $designDir . $previewImage;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
                $cnt++;
            }
        }

        /*save png url on server*/
        $cnt = 0;
        $designPngNames = array();
        $image = '';
        if(isset($request['png_url']) && !empty($request['png_url'])) {
            $pngUrls[] = $request['png_url'];
            foreach ($pngUrls as $pngUrl) {
                //$pngUrl = $request['png_url'];
                $dt = explode($this->_outputHelper->getDnbTempPath(),$pngUrl);
                $png_dir_path = $this->_outputHelper->getMediaPath().$this->_outputHelper->getDnbTempPath().$dt[1];
                $png = base64_encode(file_get_contents($png_dir_path));
                $image = "designidea_".$designideaId."_".$storeId.'.png';
                //$designPngNames[$cnt] = $image;
                $pngImagePath = $designDir . $image;
                $this->_outputHelper->saveBase64($pngImagePath, $png);
            }
        }
        /** @var \Magento\Framework\Json\Helper\Data $helper */
        $helper = $this->jsonHelper;
        /*$result = [];
        $result['svg'] = $designSvgNames;
        $result['image'] = '/svg/'.$image;*/


        $options = [];
        $colorId = $this->getRequest()->getParam('color_id');
        if(isset($colorId)){
            $options['color_id'] = $colorId;
        }

        $configAreaIds = $this->getRequest()->getParam('config_area_ids');
        if(isset($colorId)){
            $options['config_area_ids'] = $configAreaIds;
        }

        $designIdeaModel = $this->_designideaFactory->create();

        $typeId = $this->getRequest()->getParam('type');

        if (!$designideaId && $typeId) {
            $designIdeaModel->setTypeId($typeId);
        }
        $designSvgs = implode(',',$designSvgNames);

        if($designIdeaModel->getSvg() == ''){
            $designIdeaModel->setStoreId(0);
            $designIdeaModel->load($designideaId);
        } else {
            $designIdeaModel->setStoreId($storeId);
            $designIdeaModel->load($designideaId);
        }


        $designIdeaData = $designIdeaModel->getData();
        $designIdeaData['product_id'] = $productId;
        $designIdeaData['image'] = $image;
        $designIdeaData['preview_image'] = $firstSidePreviewImage;
        $designIdeaData['svg'] = $designSvgs;
        $designIdeaData['options'] = $helper->jsonEncode($options);
        $designIdeaModel->addData($designIdeaData);
        $designIdeaModel->save();
        return;
        /*$designIdeaModel->setProductId($productId);
        $designIdeaModel->setImage($image);
        $designIdeaModel->setPreviewImage($firstSidePreviewImage);

        $designIdeaModel->setSvg($designSvgs);
        $designIdeaModel->setOptions($helper->jsonEncode($options));

        $designIdeaModel->save();

        $designIdeaModel->setAttributeSetId($designIdeaModel->getDefaultAttributeSetId());
        $designIdeaModel->setWebsiteIds($websiteIds);
        //$designIdeaModel->setEntityTypeId(16);
        $designIdeaModel->save();*/
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
