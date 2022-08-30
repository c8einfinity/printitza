<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Plugin\Restriction;

/**
 * Class SalesOrderCollectionFactory
 * @author BrainActs Core Team <support@brainacts.com>
 */
class SalesInvoiceCollectionFactory
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    private $config;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * SalesOrderCollectionFactory constructor.
     * @param \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     */
    public function __construct(
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
    
        $this->config = $configFactory->create();
        $this->session = $adminSession;
    }

    /**
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject
     * @param \Closure $proceed
     * @param $requestName
     * @return mixed
     */
    public function aroundGetReport(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $subject,
        \Closure $proceed,
        $requestName
    ) {
    

        $result = $proceed($requestName);
        if ($requestName == 'sales_order_invoice_grid_data_source') {
            if ($result instanceof \Magento\Sales\Model\ResourceModel\Order\Invoice\Grid\Collection &&
                $this->config->isRestrictOrder()) {
                $role = $this->session->getUser()->getRole()->getId();
                $roles = $this->config->getRoleFullAccess();
                if (in_array($role, $roles)) {
                    return $result;
                }

                $salesrepMemberOrderTable = $result->getResource()->getTable('brainacts_salesrep_member_order');
                $salesrepMember = $result->getResource()->getTable('brainacts_salesrep_member');


                $result->join($salesrepMemberOrderTable, $salesrepMemberOrderTable . '.order_id = main_table.order_id', ['member_id']);
                $result->join($salesrepMember, $salesrepMember . '.member_id = ' . $salesrepMemberOrderTable . '.member_id', ['user_id']);
                $result->getSelect()->where('user_id = ?', $this->session->getUser()->getId());
            }
        }

        return $result;
    }
}
