<?php
/**
 * Created by PhpStorm.
 * User: Ashok
 * Date: 08-Jun-17
 * Time: 2:25 PM
 */

namespace Designnbuy\Reseller\Model\Config\Source;

class Role implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $adminRoles = $objectManager->create('Magento\Authorization\Model\Role')->getCollection()->toOptionArray();
        return $adminRoles;
    }
}