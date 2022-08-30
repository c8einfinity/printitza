<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class SaveBeforeObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;
	
	protected $aclRetriever;
	protected $_storeManager;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
		\Magento\Authorization\Model\Acl\AclRetriever $aclRetriever,
        \Magento\Framework\AuthorizationInterface $authorization,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->_objectManager = $objectManager;
		$this->aclRetriever = $aclRetriever;
        $this->helper = $helper;
        $this->_request = $request;
		$this->_storeManager = $storeManager;
        $this->_authorization = $authorization;
        $this->_authSession = $authSession;
		
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getModuleName() == 'api')
            return;

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();
		$rule = $this->helper->currentRule();
        if ((!$this->_authorization->isAllowed('Amasty_Rolepermissions::edit_products')) && (!$this->_authorization->isAllowed('Amasty_Rolepermissions::save_products'))) {
            $this->helper->redirectHome();
        }
		
		$role_data = $this->_authSession->getUser()->getRole();
		/* full excess to user to create and edit product */
		if($this->_authorization->isAllowed('Amasty_Rolepermissions::save_products') && $this->_authorization->isAllowed('Amasty_Rolepermissions::edit_products')){
		}
		else{
			/* compare product price with main store price */
			$_productId = $observer->getProduct()->getId();
			$storeId = $this->_storeManager->getDefaultStoreView()->getStoreId();
			$_mainstoreProduct = $this->_objectManager->get('Magento\Catalog\Model\Product')->setStoreId($storeId)->load($_productId);
			$_mainstorePrice = $_mainstoreProduct->getPrice();
			$_mainstoreSpecialPrice = $_mainstoreProduct->getSpecialPrice();

			if($_mainstorePrice > $product->getPrice() || $_mainstoreSpecialPrice > $product->getSpecialPrice()){
			//if($_mainstorePrice >= $product->getPrice()){
               // echo 'main-'.number_format($_mainstorePrice,2).'--k--'.$product->getPrice();die;
				$this->helper->priceError();
			}
			
			/* reseller only able to edit product price,name,qty,status */
			$unsetattribute = array("name", "qty", "sku","status","type_id","entity_id","old_id","price","special_price","description","short_description","special_from_date","special_to_date","cost","tier_price");
			$attributes = $product->getAttributes();
			foreach ($attributes as $attribute) {
					$code = $attribute->getAttributeCode();
					if (in_array($code, $unsetattribute))
					{
						
					}
					else
					{
						$product->unsetData($code);
					}
			}
		}
      
        if (!$rule->checkProductPermissions($product)
            && !$rule->checkProductOwner($product)
        ) {
            $this->helper->redirectHome();
        }

        if ($rule->getScopeStoreviews()) {
            $orig = $this->_objectManager->get('Magento\Catalog\Model\ResourceModel\Product')->getWebsiteIds($product);
            $new = $product->getData('website_ids');

            if ($orig != $new && !is_null($new)) {
                $ids = $this->helper->combine($orig, $new, $rule->getPartiallyAccessibleWebsites());
                $product->setWebsiteIds($ids);
            }

            if (!$product->getId()) {
                $product->setData('amrolepermissions_disable');
            }
        }

        if ($rule->getCategories()) {
            $ids = $this->helper->combine(
                $product->getOrigData('category_ids'),
                $product->getCategoryIds(),
                $rule->getCategories()
            );
            $product->setCategoryIds($ids);
        }

        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::product_owner')
            && $this->_authSession->getUser()) {
            $product->unsetData('amrolepermissions_owner');
        }
    }
}
