<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\AuthenticationException;
use Magento\Framework\Exception\State\UserLockedException;
use Magento\Security\Model\SecurityCookie;

class UpdateProductCommission extends \Magento\Backend\App\Action
{
    const PERCENTAGE = 1;

    const FIXED = 2;
    /**
     *
     * @var \Designnbuy\Reseller\Model\Resellers
     */
    protected $_reseller;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\User\Model\UserFactory $userFactory
     */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Reseller\Helper\Data $helper,
        \Designnbuy\Reseller\Model\ResourceModel\Products\Collection $productsFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Designnbuy\Reseller\Model\Resellers $reseller,
        array $data = []
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->productsFactory = $productsFactory;
        $this->productCollection = $productCollection;
        $this->_reseller  = $reseller;
    }

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $resellerId = $this->getRequest()->getParam('id');
            if (!$resellerId) {
                $this->messageManager->addErrorMessage(__('This Reseller no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                return $this->_redirect('*/*/');
            } else {

                $resellerModel = $this->_reseller->load($resellerId);
                $resellerWebsiteId = $resellerModel->getWebsiteId();
                $storeId = $resellerModel->getStoreId();
                
                if (isset($data['commission_type'])) {
                    $commissionType = $data['commission_type'];
                } else {
                    $commissionType = $resellerModel->getCommissionType();
                }
                if(isset($data['product_commission']) && $data['product_commission'] != "") {
                    $productCommission = $data['product_commission'];
                }else{
                    if ($resellerModel->getProductCommission() && $resellerModel->getProductCommission() != "") {
                        $productCommission = $resellerModel->getProductCommission();
                    } else {
                        $this->messageManager->addErrorMessage(__('Add Product Commission.'));
                        return $this->_redirect('*/*/edit', ['reseller_id' => $this->getRequest()->getParam('id')]);                    
                    }
                }

                $productPoolIds = $resellerModel->getProductpool();
                $productPool = $this->productsFactory;
                $productPool->addFieldToSelect('product_id');
                $productIds = array();
                $productPool->addFieldToFilter('productpool_id', array($productPoolIds));

                foreach ($productPool->getData() as $value) {
                    $productIds[] = $value['product_id'];
                }
                $productIdsSet = '';
                if($productIds):
                    $productIdsSet = implode(",", $productIds);
                endif;

                $productCollection = $this->productCollection->create();
                $productCollection->addFieldToFilter('entity_id', array('in' => $productIdsSet));            
                $productCollection->addWebsiteFilter($resellerWebsiteId);
                $productCollection->addAttributeToSelect('*');

                foreach ($productCollection as $key => $_product) {
                    if($commissionType == self::PERCENTAGE): // Percentage price calculation
                        $_price = $_product->getPrice();
                        $commission = $_price * $productCommission / 100;
                        $finalPrice = $_price + $commission;
                    elseif($commissionType == self::FIXED):
                        $_price = $_product->getPrice();
                        $finalPrice = $_price + $productCommission;
                    endif;
                    if(isset($_price) && $_price != ''):
                        $updateAttributesData = ['product_commission' => $productCommission, 'commission_type' => $commissionType, 'price' => $finalPrice, 'cost' => $_price];
                        $this->_objectManager->get('Magento\Catalog\Model\Product\Action')->updateAttributes([$_product->getId()], $updateAttributesData, $storeId);
                    endif;
                }

                $resellerModel->setCommissionType($commissionType);
                $resellerModel->setProductCommission($productCommission);
                $resellerModel->save();
                $this->messageManager->addSuccess(__('You save reseller successfully.'));
            }
            return $this->_redirect('*/*/edit', ['reseller_id' => $this->getRequest()->getParam('id')]);
            
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
