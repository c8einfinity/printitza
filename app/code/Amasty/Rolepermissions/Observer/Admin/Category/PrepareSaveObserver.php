<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class PrepareSaveObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_objectManager = $objectManager;
        $this->helper = $helper;
        $this->_request = $request;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->helper->currentRule();

        if ($rule->getProducts() || $rule->getScopeStoreviews()) {
            if (false === $rule->getAllowedProductIds())
                return;

            $category = $observer->getCategory();

            $new = $category->getPostedProducts();

            $old = [];
            foreach ($category->getProductCollection() as $id => $product) {
                $old[$id] = $product->getCatIndexPosition();
            }

            $ids = $this->helper->combine(
                array_keys($old), array_keys($new), $rule->getAllowedProductIds()
            );

            $priorities = $new + $old;

            foreach ($priorities as $k => $v) {
                if (!in_array($k, $ids))
                    unset($priorities[$k]);
            }

            $category->setPostedProducts($priorities);
        }
    }
}
