<?php
namespace Designnbuy\Canvas\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    protected $productCollection;

    protected $templateCollectionFactory;
    /**
     * Router constructor.
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->productCollection = $productCollection;
        $this->templateCollectionFactory = $templateCollectionFactory;
    }

    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        
        $identifier = trim($request->getPathInfo(), '/');
        $identifier_remove_html = str_replace(".html","",$identifier);
        
        $identifierParams = explode("/",$identifier_remove_html);
        
            //echo "<pre>fasdfsa"; print_r($request->getParams()); exit;
        if(count($identifierParams) == 2){
            
            if(isset($identifierParams[0]) && $identifierParams[0] != "" && isset($identifierParams[1]) && $identifierParams[1] != ""){
                try {
                    $prodCollection = $this->productCollection->addAttributeToFilter('url_key',$identifierParams[0])->getFirstItem();
                } catch (\Exception $e) {
                    //echo $e->getMessage(); exit;
                    //$this->renderDefaultMerchandise($request);
                }
                
                try {
                    $templateCollection = $this->templateCollectionFactory->create()->addAttributeToFilter('identifier',$identifierParams[1])->getFirstItem();
                } catch (\Exception $e) {
                    //echo $e->getMessage(); exit;
                    //$this->renderDefaultMerchandise($request);
                }
        
                if($prodCollection->getId() && $templateCollection->getId()){

                    $paramsData = [];
                    if($request->getParam('product_params') != "")
                    {
                        $paramsData = json_decode($request->getParam('product_params'),true);
                        $request->setParam('product_params','');
                    }

                    $paramsData['template_id'] = $templateCollection->getId();
                    $paramsData['id'] = $prodCollection->getId();

                    $request->setModuleName('canvas');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                    $request->setParams($paramsData);
                    
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                    );
                    
                } else {
                    //$this->renderDefaultMerchandise($request);
                }
            } else {
                //$this->renderDefaultMerchandise($request);
            }
            
        }
        
        return null;

    }

    private function renderDefaultMerchandise($request){
        
        $request->setModuleName('merchandise');
        $request->setControllerName('index');
        $request->setActionName('index');
        $request->setParams([
            'designidea_id' => 1,
            'id' => 1
        ]);
    }
}