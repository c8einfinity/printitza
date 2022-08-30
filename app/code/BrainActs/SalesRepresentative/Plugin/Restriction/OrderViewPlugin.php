<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Plugin\Restriction;

use BrainActs\SalesRepresentative\Model\ResourceModel\Links\Collection;
use Magento\Sales\Controller\Adminhtml\Order\View;

/**
 * Class OrderViewPlugin
 * @author BrainActs Core Team <support@brainacts.com>
 */
class OrderViewPlugin
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
     * @var \Magento\Framework\Controller\ResultFactory
     */
    private $resultFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var Collection
     */
    private $collection;

    /**
     * OrderViewPlugin constructor.
     * @param \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     */
    public function __construct(
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Links\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->config = $configFactory->create();
        $this->session = $adminSession;
        $this->resultFactory = $resultFactory;
        $this->url = $url;
        $this->collection = $collectionFactory->create();
    }

    public function afterExecute(View $subject, $result)
    {

        if ($this->config->isRestrictOrder()) {
            $role = $this->session->getUser()->getRole()->getId();
            $roles = $this->config->getRoleFullAccess();
            if (in_array($role, $roles)) {
                return $result;
            }
            $orderId = $subject->getRequest()->getParam('order_id');
            $table = $this->collection->getResource()->getTable('brainacts_salesrep_member');

            $this->collection->join($table, $table . '.member_id = main_table.member_id', 'user_id');
            $this->collection->addFieldToFilter('order_id', ['eq' => $orderId]);
            $this->collection->addFieldToFilter('user_id', ['eq' => $this->session->getUser()->getId()]);


            if ($this->collection->getSize()) {
                return $result;
            }

            $result = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
            $result->setUrl($this->url->getUrl('sales/order/index'));
        }

        return $result;
    }
}
