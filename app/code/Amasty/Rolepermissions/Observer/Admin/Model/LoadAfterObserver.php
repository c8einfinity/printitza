<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Model;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Model\AbstractModel;

class LoadAfterObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_coreRegistry = $registry;
        $this->_request = $request;
        $this->_objectManager = $objectManager;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->_coreRegistry->registry('current_amrolepermissions_rule');

        if (!$rule)
            return;

        $this->helper = $this->_objectManager->get('\Amasty\Rolepermissions\Helper\Data');

        if ($this->_request->getModuleName() == 'api')
            return;

        /** @var AbstractModel $object */
        $object = $observer->getObject();

        if (in_array($this->_request->getActionName(), ['edit', 'view']) && $object->getId()) {
            if ($this->helper->canSkipObjectRestriction())
                return;

            $idParam = $this->_request->getParam('id');
            if ($idParam && $object->getId() != $idParam)
                return;

            $controllerName = $this->_request->getControllerName();
            if ($this->_request->getModuleName() == 'sales' && !($object instanceof \Magento\Sales\Model\AbstractModel))
                return;

            if ($controllerName == 'product' && !($object instanceof \Magento\Catalog\Model\Product))
                return;

            if ($controllerName == 'customer') {
                if (!($object instanceof \Magento\Customer\Model\Customer))
                    return;
            }

            if ($rule->getScopeStoreviews()) {
                $this->helper->restrictObjectByStores($object->getData());
            }
        }
    }
}
