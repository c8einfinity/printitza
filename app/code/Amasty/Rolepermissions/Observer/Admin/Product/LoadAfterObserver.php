<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class LoadAfterObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->helper = $helper;
        $this->_request = $request;
        $this->_authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->_request->getModuleName() == 'api')
            return;

        if ($this->_request->getModuleName() !== 'catalog' && $this->_request->getControllerName() !== 'product')
            return;

        $rule = $this->helper->currentRule();

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $observer->getProduct();

        if ($product->getData('_edit_mode')
            && !$rule->checkProductOwner($product)
        ) { // Indexer fix
            if (!$rule->checkProductPermissions($product))
                $this->helper->redirectHome();

            if ($rule->getCategories()) {
                $productCategories = $product->getCategoryIds();
                if (!array_intersect($productCategories, $rule->getCategories())) {
                    $this->helper->redirectHome();
                }
            }
        }

        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_products')) {
            $product->setIsDeleteable(false);
        }
    }
}
