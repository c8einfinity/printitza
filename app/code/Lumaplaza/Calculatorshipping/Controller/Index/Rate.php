<?php

namespace Lumaplaza\Calculatorshipping\Controller\Index;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Exception\NoSuchEntityException;
use Lumaplaza\Calculatorshipping\Helper\Data as CalculatorshippingData;


class Rate extends \Magento\Checkout\Controller\Cart
{
	/**
     * @var estshippingcost Data
     */
    protected $_estshippingcostData;
	
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
	
	/**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_itemFactory;

    protected $_wishlistProvider;

    protected $_resultJsonFactory;
	protected $_shipModel;
	protected $_checkoutSession;


    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param CustomerCart $cart
     * @param ProductRepositoryInterface $productRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        CustomerCart $cart,
		CalculatorshippingData $calshippingData,
		\Magento\Framework\Registry $registry,
        ProductRepositoryInterface $productRepository,
        \Magento\Wishlist\Model\ItemFactory $itemFactory,
		\Magento\Shipping\Model\Shipping $shippingModel,
        \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider,
        \Magento\Framework\Controller\Result\JsonFactory  $resultJsonFactory
    ) {
        parent::__construct(
            $context,
            $scopeConfig,
            $checkoutSession,
            $storeManager,
            $formKeyValidator,
            $cart
        );
        $this->productRepository = $productRepository;
		$this->_calshippingData = $calshippingData;
		$this->_coreRegistry = $registry;
		$this->_itemFactory = $itemFactory;
		$this->_wishlistProvider = $wishlistProvider;
		$this->_resultJsonFactory = $resultJsonFactory;
		$this->_shipModel    = $shippingModel;
		$this->_checkoutSession    = $checkoutSession;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */

    public function execute()
    {
		$json_encode = array();
		$params = $this->getRequest()->getParams();	
		
		if(isset($params['include_cart']) && $params['include_cart']){
			$quote = $this->_checkoutSession->getQuote();
		} else {
			$quote = $this->_objectManager->create('Magento\Quote\Model\Quote');
		}
		
		$result = $this->_resultJsonFactory->create();
		
		if(isset($params['ajax']) && $params['ajax']){		
			try {
				$product = $this->_initProduct();	
				
				/* will hide form -> comment this code */
				/* if(isset($params['include_cart']) && $params['include_cart']){
					$quote = $this->_checkoutSession->getQuote();
				} else {
					if($product->getTypeId() == 'downloadable' || $product->getTypeId() == 'virtual') {
						$json_encode["shipping_estimaste"] = __("Free Shipping");
						$json_encode["error"] = 0;
						return $result->setData($json_encode);
					}
				} */

				if (isset($params['qty'])) {
					$filter = new \Zend_Filter_LocalizedToNormalized(
						['locale' => $this->_objectManager->get('Magento\Framework\Locale\ResolverInterface')->getLocale()]
					);
					$params['qty'] = $filter->filter($params['qty']);
				}

				/**
				 * Check product availability
				 */
				if (!$product) {
					$json_encode["error"] = 1;
					$json_encode["error_msg"] = __("The product is not available");
					return $result->setData($json_encode);
				}

				/* return options popup content when product type is grouped */

				if($this->_calshippingData->checkVersion()){
					$productConfig = $product->getTypeId() != 'downloadable' && $product->getHasOptions()
						|| ($product->getTypeId() == 'grouped' && !isset($params['super_group']))
						|| ($product->getTypeId() == 'configurable' && !isset($params['super_attribute']))
						|| $product->getTypeId() == 'bundle';
				} else {
					$productConfig = $product->getHasOptions()
						|| ($product->getTypeId() == 'grouped' && !isset($params['super_group']))
						|| ($product->getTypeId() == 'configurable' && !isset($params['super_attribute']))
						|| $product->getTypeId() == 'bundle';
				}

				if($params['utype'] != "detail-add") {
					if ($productConfig || $params['utype'] == "general-add") {
						$this->_coreRegistry->register('product', $product);
						$this->_coreRegistry->register('current_product', $product);

						$json_encode["popup_option"] = 1;

						$htmlPopup = $this->_calshippingData->getPopupOptionHtml($product);
						$json_encode['html_popup_option'] = $htmlPopup;
						
						return $result->setData($json_encode);			
					}
				}
				
				if(isset($params["estimate"])) {
					$post_code = ""; $region_id = ""; $region = "";
					if(isset($params["estimate"]["post_code"])) {
						$post_code = $params["estimate"]["post_code"];
					}
					
					if(isset($params["estimate"]["region_id"])) {
						$region_id = $params["estimate"]["region_id"];
					}
					
					if(isset($params["estimate"]["region"])) {
						$region = $params["estimate"]["region"];
					}
					
					$quote->getShippingAddress()
							->setCountryId($params["estimate"]["country_id"]) 
							/* ->setCity($params["estimate"]["city"]) */ 
							->setPostcode($post_code) 
							->setRegionId($region_id) 
							->setRegion($region);

					$_requestInfo = new \Magento\Framework\DataObject($params);
					$quote->addProduct($product, $_requestInfo);
					
					$quote->collectTotals();
					$quote->getShippingAddress()->setCollectShippingRates(true);
					$quote->getShippingAddress()->collectShippingRates();
					
					$address = $quote->getShippingAddress();
					$all_shipping_rates = $address->getAllShippingRates();
					
					$shippingPrices = array();
					$shipping_carrier = array();
					$shipping_title = array();
					foreach($all_shipping_rates as $rate){
						if(!in_array($rate->getCarrier(), $shipping_carrier)) {
							$shipping_carrier[] = $rate->getCarrier();
							$shipping_title[] = $rate->getCarrierTitle();
						}
					}
				
					$html = "";

					for($i = 0; $i < count($shipping_carrier); $i++) {
						$html .= '<label class="label">'.$shipping_title[$i].'</label>';
						foreach($all_shipping_rates as $rate){
							if($shipping_carrier[$i] == $rate->getCarrier()) {
								$html .= '<span class="shipping-method-title">'.$rate->getMethodTitle(). (($rate->getMethodTitle()) ? " - " : "  ") . $this->_calshippingData->showPrice($rate->getPrice()).'</span>';
							}
						}
					}
					
					$json_encode["shipping_estimaste"] = $html;
				
				} /* endif $params["estimate"] */

			} catch (\Magento\Framework\Exception\LocalizedException $e) {
				if ($this->_checkoutSession->getUseNotice(true)) {
					$this->messageManager->addNotice(
						$this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($e->getMessage())
					);
					$json_encode["error_msg"] = $e->getMessage();
				} else {
					$messages = array_unique(explode("\n", $e->getMessage()));
					foreach ($messages as $message) {
						$json_encode["error_msg"] = $message;
					}
				}
				$json_encode["error"] = 1;			
			} catch (\Exception $e) {
				$json_encode["error"] = 1;
				$json_encode["error_msg"] = $e->getMessage();
				$this->messageManager->addError( __($json_encode["error_msg"]));
			}
		}	

		$json_encode["error"] = 0;
		return $result->setData($json_encode);
    }
}
