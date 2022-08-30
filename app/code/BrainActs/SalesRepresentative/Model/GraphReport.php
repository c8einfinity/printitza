<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

/**
 * Class GraphReport
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class GraphReport
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * GraphReport constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory
     * @param ResourceModel\Report\Profit\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Reports\Model\ResourceModel\Report\Collection\Factory $resourceFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->registry = $registry;
        $this->resourceFactory = $resourceFactory;
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @param string $period
     * @param $member
     * @return array
     */
    //@codingStandardsIgnoreStart
    public function prepareReportData($period = '7d', $member)
    {
        $report = [];

        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit\Collection $resourceCollection */
        $resourceCollection = $this->resourceFactory->create(
            \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit\Collection::class
        );

        $storeIds = $this->_getStoreIds();
        $storeIds[] = 0;

        $end = date('Y-m-d');

        switch ($period) {
            case '1m':
                $date = strtotime("-1 month");

                break;
            case '1y':
                $date = strtotime("-1 year");
                break;
            case '7d':
            default:
                $date = strtotime("-7 day");
                break;
        }

        $start = date('Y-m-d', $date);
        $connection = $resourceCollection->getConnection();

        $table = $connection->getTableName('brainacts_salesrep_report_profit');
        $query = "select period, SUM(earn) as earn from {$table} where period>='$start' AND period <= '$end' and member_id={$member->getMemberId()}  group by period order by period ASC";

        $result = $connection->fetchAll($query);

        foreach ($result as $item) {
            $report[] = [$item['period'], $item['earn']];
        }

        return $report;
    }


    /**
     * Get allowed store ids array intersected with selected scope in store switcher
     *
     * @return array
     */
    protected function _getStoreIds()
    {
        $storeIds = [];

        // By default storeIds array contains only allowed stores
        $allowedStoreIds = array_keys($this->storeManager->getStores());
        // And then array_intersect with post data for prevent unauthorized stores reports
        $storeIds = array_intersect($allowedStoreIds, $storeIds);
        // If selected all websites or unauthorized stores use only allowed
        if (empty($storeIds)) {
            $storeIds = $allowedStoreIds;
        }
        // reset array keys
        $storeIds = array_values($storeIds);

        return $storeIds;
    }


}