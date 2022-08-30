<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class BlockCreateAfterObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry,
        \Amasty\Rolepermissions\Helper\Data $helper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Backend\Helper\Data $backendHelper
    ) {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
        $this->helper = $helper;
        $this->_request = $request;
        $this->_response = $response;
        $this->backendHelper = $backendHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!($observer->getBlock() instanceof \Magento\Backend\Block\Store\Switcher))
            return;

        $rule = $this->helper->currentRule();

        if (!$this->_request->getParam('store') && !$this->_request->getParam('website')) { // "All store views"
            $views = $rule->getScopeStoreviews();
            if ($views) { // Redirect to first available store view
                $redirectUrl = $this->backendHelper->getUrl('*/*/*', [
                    '_current'  => true,
                    'store'     => $views[0]
                ]);

                $this->_response->setRedirect($redirectUrl);
            }
        }
    }
}
