<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class MassDeletePredispatchObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Amasty\Rolepermissions\Helper\Data $helper
    ) {
        $this->_authorization = $authorization;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::delete_products')) {
            $this->helper->redirectHome();
        }

        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        $ids = $request->getParam('selected');

        $rule = $this->helper->currentRule();
        $productRestriction = $rule->getAllowedProductIds(); // allow to delete own products

        if ($productRestriction === false)
            return;

        $diff = array_diff($ids, $productRestriction);
        if (!empty($diff)) {
            $this->helper->redirectHome();
        }
    }
}
