<?php
namespace Designnbuy\CustomerPhotoAlbum\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerPhotoAlbum implements  ObserverInterface
{

    protected $_registry;

    protected $attributeSet;

    protected $request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\State $appState,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
    )
    {       
        $this->_registry = $registry;
        $this->appState = $appState;
        $this->request = $request;
        $this->attributeSet = $attributeSet;
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $moduleName = $this->request->getModuleName();
        $controller = $this->request->getControllerName();
        $action     = $this->request->getActionName();
        if($this->appState->getAreaCode() != \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE && $moduleName.'_'.$controller."_".$action == 'catalog_product_view'){
            
            $layout = $observer->getLayout();
            $_product = $this->_registry->registry('current_product');
            
            $attributeSetRepository = $this->attributeSet->get($_product->getAttributeSetId());
            
            if ($_product && $attributeSetRepository->getAttributeSetName() == 'PhotoPrint') {
                if ($_product && $_product->hasOptions() && count($_product->getOptions()) > 0 ) {
                    
                    $layout->getUpdate()->addHandle('catalog_product_view_photoalbum_option');
                } else {
                    $layout->getUpdate()->addHandle('catalog_product_view_photoalbum');
                }
            }
        }

        return $this;
    }
}