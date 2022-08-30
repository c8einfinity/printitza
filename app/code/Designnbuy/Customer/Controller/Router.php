<?php
namespace Designnbuy\Customer\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    protected $actionFactory;

    protected $productCollection;

    protected $templateCollectionFactory;
    
    protected $designCollectionFactory;
    /**
     * Router constructor.
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection,
        \Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory $designCollectionFactory
    ) {
        $this->actionFactory = $actionFactory;
        $this->productCollection = $productCollection;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->designCollectionFactory = $designCollectionFactory;
    }

    /**
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        //echo "<pre>"; print_r($request->getPathInfo()); exit;
        $identifier = trim($request->getPathInfo(), '/');
        $identifier_remove_html = str_replace(".html","",$identifier);
        
        $identifierParams = explode("/",$identifier_remove_html);
        
        //echo "<pre>fasdfsa"; print_r($request->getParams()); exit;
        if(count($identifierParams) == 3){
            
            if(isset($identifierParams[0]) && $identifierParams[0] != "" && isset($identifierParams[1]) && $identifierParams[1] != ""){
                try {
                    $prodCollection = $this->productCollection->addAttributeToFilter('url_key',$identifierParams[0])->getFirstItem();
                } catch (\Exception $e) {
                    //echo $e->getMessage(); exit;
                    //$this->renderDefaultMerchandise($request);
                }
                
                /* try {
                    //echo "<pre>"; print_r(get_class_methods($this->designCollectionFactory->create())); exit;
                    $templateCollection = $this->designCollectionFactory->create()->getItemById($identifierParams[1]);
                    
                } catch (\Exception $e) {
                    //echo $e->getMessage(); exit;
                    //$this->renderDefaultMerchandise($request);
                } */
                
                if($prodCollection->getId() && $prodCollection->getAttributeSetId() == 9){
                    $request->setModuleName('canvas');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                    $request->setParams([
                        'id' => $prodCollection->getId(),
                        'design_id' => $identifierParams[1]
                    ]);
                    
                    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

                    return $this->actionFactory->create(
                        'Magento\Framework\App\Action\Forward',
                        ['request' => $request]
                    );
                    
                } else if ($prodCollection->getId() && $prodCollection->getAttributeSetId() == 10){
                    $request->setModuleName('merchandise');
                    $request->setControllerName('index');
                    $request->setActionName('index');
                    $request->setParams([
                        'id' => $prodCollection->getId(),
                        'design_id' => $identifierParams[1]
                    ]);
                    
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