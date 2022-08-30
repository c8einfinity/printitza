<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Rule;

use Magento\Framework\Event\ObserverInterface;

class PrepareObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Registry $registry
    )
    {
        $this->_objectManager = $objectManager;
        $this->_coreRegistry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $rule = $this->_objectManager->create('Amasty\Rolepermissions\Model\Rule');

        if ($rid = +$observer->getRequest()->getParam('rid')) {
            $rule->load($rid, 'role_id');
        }

        $this->_coreRegistry->register('amrolepermissions_current_rule', $rule);
    }
}
