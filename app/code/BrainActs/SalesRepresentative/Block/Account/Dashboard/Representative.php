<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Account\Dashboard;

use Magento\Framework\View\Element\Template;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Representative
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Representative extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    private $config;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory
     */
    private $memberCollectionFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory
     */
    private $memberResourceFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    private $currentCustomer;


    /**
     * Representative constructor.
     * @param Template\Context $context
     * @param \BrainActs\SalesRepresentative\Model\Config $config
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory $memberCollectionFactory
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \BrainActs\SalesRepresentative\Model\Config $config,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory $memberCollectionFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->memberCollectionFactory = $memberCollectionFactory;
        $this->memberResourceFactory = $memberResourceFactory;
        $this->currentCustomer = $currentCustomer;
    }

    /**
     * Return list of active members
     * @return \BrainActs\SalesRepresentative\Model\ResourceModel\Member\Collection
     */
    public function getMembers()
    {
        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member\Collection $collection */
        $collection = $this->memberCollectionFactory->create();
        $collection->addFieldToFilter('is_active', ['eq' => 1]);

        return $collection;
    }

    /**
     * Get List of members that assigned to the current customer
     * @return array
     */
    public function getCurrentMembers()
    {
        $resource = $this->memberResourceFactory->create();
        $ids = $resource->getMembersByCustomer($this->getCustomer()->getId());

        return $ids;
    }

    /**
     * Returns the Magento Customer Model for this block
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     */
    private function getCustomer()
    {
        try {
            return $this->currentCustomer->getCustomer();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Return submit url for update sr form
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('salesrep/customer/update', ['_current' => true]);
    }

    /**
     * @return bool
     */
    public function isAllowEdit()
    {
        return $this->config->isAllowChange();
    }

    /**
     * @return bools
     */
    public function isEnabled()
    {
        return $this->config->isActiveFront();
    }

    /**
     * Return json config to load js sales representative plugin on customer dashboard page
     * @return string
     */
    public function getJsonConfig()
    {
        $config = [
            'salesrep' => [
            ]
        ];
        return json_encode($config);
    }

    /**
     *
     */
    public function isRequestExist()
    {
        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member $resource */
        $resource = $this->memberResourceFactory->create();

        return $resource->isExistRequest($this->getCustomer()->getId());
    }


}
