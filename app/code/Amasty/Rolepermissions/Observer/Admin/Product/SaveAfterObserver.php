<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Catalog\Model\Product\Attribute\Source\Status as ProductStatus;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;


    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->helper = $helper;
        $this->_request = $request;
        $this->_authSession = $authSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getModuleName() == 'api')
            return;

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        if (!$product->getOrigData('entity_id')) {
            if (!$product->getAmrolepermissionsOwner()) {
                $user = $this->_authSession->getUser();

                if ($user) {
                    $product->setAmrolepermissionsOwner($user->getId());
                    $product->getResource()->saveAttribute($product, 'amrolepermissions_owner');
                }
            }
        }

        if ($product->hasData('amrolepermissions_disable')) {
            $rule = $this->helper->currentRule();

            $resource = $product->getResource();

            $status = $product->getStatus();

            $product->setStatus(ProductStatus::STATUS_DISABLED);
            $resource->saveAttribute($product, 'status');

            $resource->getAttribute('status')->setIsGlobal(ScopedAttributeInterface::SCOPE_STORE);

            $preservedStoreId = $product->getStoreId(); // Just in case

            $product->setStatus($status);
            foreach ($rule->getScopeStoreviews() as $storeId) {
                $product->setStoreId($storeId);
                $resource->saveAttribute($product, 'status');
            }

            $product->setStoreId($preservedStoreId);
        }
    }
}
