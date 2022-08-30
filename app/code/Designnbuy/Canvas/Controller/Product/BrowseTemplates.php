<?php

namespace Designnbuy\Canvas\Controller\Product;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Index
 * @package Designnbuy\Orderattachment\Controller\Index
 */
class BrowseTemplates extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $coreSession;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_product;

    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
     
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ProductFactory $_product,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->coreSession = $coreSession;
        $this->_coreRegistry = $coreRegistry;
        $this->_product = $_product;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        //echo "bhjbhj"; exit;
        $resultPageFactory = $this->resultPageFactory->create();
         if (!empty($this->getRequest()->getParams())) {
            
            $this->coreSession->setRefererUrl($this->_redirect->getRefererUrl());

            $breadcrumbs = $resultPageFactory->getLayout()->getBlock('breadcrumbs');
            $breadcrumbs->addCrumb('home', [
                'label' => __('Home'),
                'title' => __('Home'),
                'link' => $this->_url->getUrl('')
                    ]
            );
            
            //echo "<pre>"; print_r($this->getRequest()->getParams()); exit;
            if($this->getRequest()->getParam('product')){
                $product = $this->_product->create()->load($this->getRequest()->getParam('product'));
                if($product){
                    $this->_coreRegistry->register('current_product',$product);
                    $this->_coreRegistry->register('product',$product);
                    
                    $breadcrumbs->addCrumb('product_name', [
                        'label' => __($product->getName()),
                        'title' => __($product->getName()),
                        'link' => $product->getProductUrl()
                            ]
                    );
                    
                    if ($this->getRequest()->getParam('category_id')) {
                        
                        if($this->getRequest()->getParam('product_params')){
                            $product_params = json_decode($this->getRequest()->getParam('product_params'),true);
                            $this->getRequest()->setParams($product_params);
                            $this->getRequest()->setParam('product_params','');
                        }

                        $layout = $resultPageFactory->getLayout();
                        $html = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                        ->setTemplate('Designnbuy_Template::catalog/product/relatedtemplates_v2.phtml');

                        if ($this->getRequest()->getParam('p')) {

                            $paginationHtml = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                            ->setTemplate('Designnbuy_Template::catalog/product/customPagination.phtml');
                        
                            $html->setChild('pagination',$paginationHtml);

                        }

                        $result = [];
                        $result['category_templates'] = $html->toHtml();

                        $this->getResponse()->setHeader('Content-type', 'application/json');
                        return $this->getResponse()->setBody(json_encode($result));

                    } 
                    
                    if ($this->getRequest()->getParam('height') && $this->getRequest()->getParam('width') || $this->getRequest()->getParam('search')) {
                        
                        if($this->getRequest()->getParam('product_params')){
                            $product_params = json_decode($this->getRequest()->getParam('product_params'),true);
                            $this->getRequest()->setParams($product_params);
                            $this->getRequest()->setParam('product_params','');
                        }

                        $layout = $resultPageFactory->getLayout();
                        $html = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                        ->setTemplate('Designnbuy_Template::catalog/product/relatedtemplates_v2.phtml');
                        
                        if ($this->getRequest()->getParam('p')) {
                            
                            $paginationHtml = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                            ->setTemplate('Designnbuy_Template::catalog/product/customPagination.phtml');

                            $html->setChild('pagination',$paginationHtml);
                        }

                        $result = [];
                        $result['category_templates'] = $html->toHtml();

                        $this->getResponse()->setHeader('Content-type', 'application/json');
                        return $this->getResponse()->setBody(json_encode($result));
                    }
                    
                    if($this->getRequest()->getParam('ajax') && $this->getRequest()->getParam('ajax') == true){

                        if($this->getRequest()->getParam('product_params')){
                            $product_params = json_decode($this->getRequest()->getParam('product_params'),true);
                            $this->getRequest()->setParams($product_params);
                            $this->getRequest()->setParam('product_params','');
                        }

                        $layout = $resultPageFactory->getLayout();
                        $html = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                        ->setTemplate('Designnbuy_Template::catalog/product/relatedtemplates_v2.phtml');
                        
                        if ($this->getRequest()->getParam('p')) {
                            
                            $paginationHtml = $layout->createBlock('Designnbuy\Template\Block\Catalog\Product\RelatedTemplatesVersionTwo')
                            ->setTemplate('Designnbuy_Template::catalog/product/customPagination.phtml');

                            $html->setChild('pagination',$paginationHtml);
                        }

                        $result = [];
                        $result['category_templates'] = $html->toHtml();

                        $this->getResponse()->setHeader('Content-type', 'application/json');
                        return $this->getResponse()->setBody(json_encode($result));

                    }
                }
            }
            
            $breadcrumbs->addCrumb('page_name', [
                'label' => __('Browse Templates'),
                'title' => __('Browse Templates'),
                    ]
            );

            $this->_view->loadLayout();
            $this->_view->renderLayout();
        } else {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            
            $refereUrl = $this->_redirect->getRefererUrl();
            if($this->coreSession->getRefererUrl()){
                $refereUrl = $this->coreSession->getRefererUrl();
                $this->coreSession->unsRefererUrl();
            }
            
            $resultRedirect->setUrl($refereUrl);
            return $resultRedirect;
        }
    }
}
