<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Config;

use Magento\Checkout\Model\ConfigProviderInterface;
use BrainActs\SalesRepresentative\Model\MemberFactory;
use BrainActs\SalesRepresentative\Model\Config;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;

/**
 * Class ConfigProvider
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class ConfigProvider implements ConfigProviderInterface
{
    /**
     * @var MemberFactory
     */
    private $memberFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlInterface;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory
     */
    private $memberResourceFactory;

    /**
     * @var SessionManagerInterface
     */
    private $customerSession;

    /**
     * ConfigProvider constructor.
     * @param MemberFactory $memberFactory
     * @param Config $config
     * @param UrlInterface $urlInterface
     * @param SessionManagerInterface|null $sessionManager
     */
    public function __construct(
        MemberFactory $memberFactory,
        Config $config,
        UrlInterface $urlInterface,
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory,
        SessionManagerInterface $sessionManager = null
    ) {
        $this->memberFactory = $memberFactory;
        $this->config = $config;
        $this->urlInterface = $urlInterface;
        $this->memberResourceFactory = $memberResourceFactory;
        $this->customerSession = $sessionManager;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        /** @var \BrainActs\SalesRepresentative\Model\Member $model */
        $model = $this->memberFactory->create();

        /**
         * Collect all active members
         */
        $collection = $model->getCollection()->addFieldToFilter('is_active', ['eq' => 1])->load();

        $members = [];

        /** @var \BrainActs\SalesRepresentative\Model\Member $member */
        foreach ($collection as $member) {
            $item['id'] = $member->getMemberId();
            $item['name'] = $member->getFirstname() . ' ' . $member->getLastname();
            $members[] = $item;
        }

        $isEnabled = $this->config->isEnabledChoose();

        if (!$this->customerSession->isLoggedIn()) {
            $isEnabled = false;
            $members = [];
        } else {
            //check if customer is assigned
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member $resourceMember */
            $resourceMember = $this->memberResourceFactory->create();
            $membersAssigned = $resourceMember->getMembersByCustomer($this->customerSession->getCustomerData()->getId());

            if (!empty($membersAssigned)) {
                $isEnabled = false;
            }
        }

        return [
            'salesrep' => [
                'members' => $members,
                'isEnabled' => $isEnabled,
                'checkoutUrl' => $this->urlInterface->getUrl('rest/V1/carts/', ['mine' => 'sales-representative'])
            ]
        ];
    }
}
