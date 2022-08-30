<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Rule;

use Magento\Framework\Event\ObserverInterface;

class SaveObserver implements ObserverInterface
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
        $role = $this->_coreRegistry->registry('current_role');

        if (!$role)
            return;

        $request = $observer->getRequest();

        $rule = $this->_objectManager->create('Amasty\Rolepermissions\Model\Rule')->load(
            $role->getId(),
            'role_id'
        );

        $data = $request->getParam('amrolepermissions');

        $rule
            ->setScopeWebsites('')
            ->setScopeStoreviews('');

        $data['role_id'] = $role->getId();

        if (isset($data['products_access_mode']))
        {
            if ($data['products_access_mode'] == \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Products::MODE_MY)
                $data['products'] = \Amasty\Rolepermissions\Model\Rule::PRODUCT_ACCESS_MODE_MY;
            else if ($data['products_access_mode'] == \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Products::MODE_ANY)
                $data['products'] = \Amasty\Rolepermissions\Model\Rule::PRODUCT_ACCESS_MODE_ANY;
            else
                $data['products'] = explode('&', $data['products']);
        }

        if (isset($data['categories_access_mode'])) {
            if ($data['categories_access_mode'] == \Amasty\Rolepermissions\Block\Adminhtml\Role\Tab\Categories::MODE_ALL) {
                $data['categories'] = [];
            }
            else {
                $data['categories'] = str_replace(' ', '', $data['categories']);
            }
        }

        $rule->addData($data);

        foreach ($rule->getData() as $key => $value)
        {
            if (is_array($value))
            {
                $rule->setData($key, implode(',', array_unique(array_filter(
                    $value, function($x){return +$x > 0;}
                ))));
            }
        }

        $rule->save();
    }
}
