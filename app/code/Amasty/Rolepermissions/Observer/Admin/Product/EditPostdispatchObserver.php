<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Product;

use Magento\Framework\Event\ObserverInterface;

class EditPostdispatchObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\ViewInterface
     */
    protected $_view;

    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    public function __construct(
        \Magento\Framework\App\ViewInterface $view,
        \Magento\Framework\AuthorizationInterface $authorization
    ) {
        $this->_view = $view;
        $this->_authorization = $authorization;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_authorization->isAllowed('Amasty_Rolepermissions::save_products')) {
            $toolbar = $this->_view->getLayout()->getBlock('page.actions.toolbar');
            $toolbar->unsetChild('save-split-button');
        }
    }
}
