<?php
/**
 * Customer attribute data helper
 */

namespace Designnbuy\Canvas\Helper;
use Magento\Framework\App\Filesystem\DirectoryList;
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;

    protected $templateCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    ) {
        $this->_imageFactory = $imageFactory;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->_storeManager = $storeManager;
        $this->templateCollectionFactory = $templateCollectionFactory;
        parent::__construct($context);
    }

    public function isFullDesignerEnable($_product){
        /*
        Quick Edit Only = 1
        Quick Edit with Full Designer = 2
        Full Designer Only = 3
        Multiple Design Templates = 4
        */

        if(($_product->getCanvasPersonalizeOption() == 1 || $_product->getCanvasPersonalizeOption() == 2 || $_product->getCanvasPersonalizeOption() == 5) && $_product->getTemplateId() != ''){
            return true;
        }
        return false;
    }



    public function getCanvasScratchPageUrl($_product)
    {
        return $this->_getUrl('canvas/index/index', array(
            'id' => $_product->getId()
        ));
    }

    public function getCanvasPageUrl($_product)
    {
        $params = [];
        if($_product->getCanvasPersonalizeOption() == 1 || $_product->getCanvasPersonalizeOption() == 2){
            $params['template_id'] =   $_product->getTemplateId();
            $params['id'] =   $_product->getId();
            if($_product->getTemplateId()){
                $templateColl = $this->templateCollectionFactory->create()
                        ->addAttributeToSelect('identifier')
                        ->getItemById($_product->getTemplateId());
                if(!empty($templateColl->getData())){
                    if($templateColl->getIdentifier() != "" && $_product->getUrlKey() != ""){
                        $template_product_url = trim($_product->getUrlKey(),'/').'/'.trim($templateColl->getIdentifier(),'/').'.html';
                        
                        return rtrim($this->_getUrl($template_product_url),'/');
                    }
                }
            }

        } else {
            $params['id'] =   $_product->getId();
        }
        return $this->_getUrl('canvas/index/index', $params);
    }

    public function getTemplatePersonalisePageUrl($_product, $_template)
    {

        if($_template->getId()){
            $templateColl = $this->templateCollectionFactory->create()
                    ->addAttributeToSelect('identifier')
                    ->getItemById($_template->getId());
            if(!empty($templateColl->getData())){
                if($templateColl->getIdentifier() != "" && $_product->getUrlKey() != ""){
                    $template_product_url = trim($_product->getUrlKey(),'/').'/'.trim($templateColl->getIdentifier(),'/').'.html';
                    
                    return rtrim($this->_getUrl($template_product_url),'/');
                }
            }
        }

        return $this->_getUrl('canvas/index/index', array(
            'id' => $_product->getId(),
            'template_id' => $_template->getId()
        ));
    }
}
