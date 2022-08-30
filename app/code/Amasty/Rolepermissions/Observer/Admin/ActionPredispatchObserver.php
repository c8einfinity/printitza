<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ObserverInterface;

class ActionPredispatchObserver implements ObserverInterface
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
        /** @var RequestInterface $request */
        $request = $observer->getRequest();

        if ($request->getControllerName() == 'cms_wysiwyg_images')
            return;

        $rule = $this->helper->currentRule();

        if (!$rule || !$rule->getScopeStoreviews())
            return;

        if ($storeId = $request->getParam('store')) {
            if (is_array($storeId)) {
                $storeId = $storeId['store_id'];
            }

            if (!in_array($storeId, $rule->getScopeStoreviews())) {
                $this->helper->redirectHome();
            }
        }
        else if ($websiteId = $request->getParam('website')) {
            if (is_array($websiteId)) {
                $websiteId = $websiteId['website_id'];
            }

            if (!$rule->hasScopeWebsites() || !in_array($websiteId, $rule->getScopeWebsites())) {
                $this->helper->redirectHome();
            }
        }
        else if ($group = $request->getParam('group')) {
            if (is_array($group)) {
                $websiteId = $group['website_id'];

                if (!$rule->hasScopeWebsites() || !in_array($websiteId, $rule->getScopeWebsites())) {
                    $this->helper->redirectHome();
                }
            }
        }
    }
}
