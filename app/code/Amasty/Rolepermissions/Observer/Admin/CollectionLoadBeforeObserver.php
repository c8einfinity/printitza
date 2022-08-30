<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Amasty\Rolepermissions\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;

class CollectionLoadBeforeObserver implements ObserverInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    public function __construct(
        \Magento\Framework\Registry $registry
    ) {
        $this->_coreRegistry = $registry;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();

        $rule = $this->_coreRegistry->registry('current_amrolepermissions_rule');

        if (!$rule)
            return;

        $config = $rule->getCollectionConfig();

        if ($rule->getScopeStoreviews()) {
            if ($collection instanceof \Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection) {
                $collection->getSelect()->join(
                    ['am_rp_order' => $collection->getTable('sales_order')],
                    'am_rp_order.entity_id = main_table.order_id',
                    []
                );

                $config = ['internal' => ['store' => ['Magento\Sales\Model\ResourceModel\Order\Payment\Transaction' => 'am_rp_order.store_id']]];
            }

            else if ($collection instanceof \Magento\Review\Model\ResourceModel\Review\Product\Collection) {
                $config = ['internal' => ['store' => ['Magento\Catalog\Model\ResourceModel\Product' => 'store.store_id']]];
            }

            else if ($collection instanceof \Amasty\Groupcat\Model\ResourceModel\Rules\Collection) {
                $config = ['internal' => ['store' => ['\Amasty\Groupcat\Model\ResourceModel\Rules' => 'main_table.stores']]];
            }
            foreach ($config as $joinType => $scopes) {
                foreach ($scopes as $scope => $collectionsConfig) {
                    foreach ($collectionsConfig as $modelType => $field) {
                        if (!($collection->getresource() instanceof $modelType))
                            continue;

                        if ($scope == 'store') {
                            $ids = $rule->getScopeStoreviews();
                            $ids []= 0;
                        }
                        else {
                            $ids = $rule->getPartiallyAccessibleWebsites();
                        }

                        if ($joinType == 'internal') {
                            $select = $collection->getSelect();

                            // sets intersection
                            $conditionSql = "";
                            foreach ($ids as $id) {
                                $conditionSql .= " OR $id IN ($field)";
                            }
                            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                            $requestInterface = $objectManager->get('Magento\Framework\App\RequestInterface');
                            $moduleName = $requestInterface->getModuleName();
                            $controller = $requestInterface->getControllerName();
                            $action     = $requestInterface->getActionName();
                            if($moduleName.'_'.$controller.'_'.$action != 'sales_order_create_index'){
                                $select->where("0 IN ($field) $conditionSql");
                            }
                        }
                        else {
                            $idField = $collection->getResource()->getIdFieldName();
                            $select = $collection->getSelect();
                            $storeSelect = clone $select;
                            $storeSelect->reset()
                                ->from(['amrolepermissions_join' => $collection->getResource()->getTable($field)], $idField)
                                ->where("amrolepermissions_join.{$scope}_id IN (?)", $ids);
                            $select->where("main_table.$idField IN ($storeSelect)");
                        }

                        return;
                    }
                }
            }
        }

        $ruleCategories = $rule->getCategories();
        if ($ruleCategories) {
            if ($collection instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {
                $collection->addFieldToFilter('entity_id', ['in' => $ruleCategories]);
            }
        }
    }
}
