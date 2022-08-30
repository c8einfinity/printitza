<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin\Category;

use Magento\Framework\Event\ObserverInterface;

class SaveAfterObserver implements ObserverInterface
{
    /** @var \Amasty\Rolepermissions\Helper\Data */
    protected $helper;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;


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
        $categoryId = $this->_request->getParam('id');
        if (!$categoryId) { // New category
            $category = $observer->getCategory();

            $this->_updateSubcategoryPermissions($category);
        }
    }

    protected function _updateSubcategoryPermissions($category)
    {
        $rules = $this->_objectManager->create('Amasty\Rolepermissions\Model\ResourceModel\Rule\Collection')
            ->addFieldToFilter('categories', ['finset' => $category->getParentId()]);

        foreach ($rules as $rule) {
            $categories = explode(',', $rule->getCategories());
            $categories[] = $category->getId();

            $rule
                ->setCategories(implode(',', array_unique($categories)))
                ->save()
            ;
        }
    }
}
