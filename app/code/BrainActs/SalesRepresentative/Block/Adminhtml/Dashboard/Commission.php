<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard;

/**
 * Class Commission
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Commission extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory
     */
    private $profitFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    public $config;

    private $lifeTimeAmount = null;

    private $availableAmount = null;

    private $pendingAmount = null;

    private $payout = null;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface|null
     */
    private $memberRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * @var \BrainActs\SalesRepresentative\Api\Data\MemberInterface
     */
    private $member;

    /**
     * @var \Magento\Framework\Authorization\PolicyInterface
     */
    private $policyInterface;

    /**
     * Commission constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory $profitFactory,
        \BrainActs\SalesRepresentative\Model\Config $config,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        \Magento\Framework\Authorization\PolicyInterface $policyInterface,
        array $data = []
    ) {
    
        $this->priceCurrency = $priceCurrency;
        $this->profitFactory = $profitFactory;
        $this->config = $config;
        $this->session = $sessionManager;
        $this->memberRepository = $memberRepository;
        $this->policyInterface = $policyInterface;
        parent::__construct($context, $data);
    }

    private function getMember()
    {
        $adminId = $this->session->getUser()->getId();
        $this->member = $this->memberRepository->getByUserId($adminId);
    }

    public function getAvailableAmount($format = true)
    {
        $this->getMember();
        if ($this->availableAmount === null) {
            $this->getLifeTimeAmount();
            $this->getPendingAmount();
            $this->getPayoutAmount();
            $this->availableAmount = $this->lifeTimeAmount - $this->pendingAmount - $this->payout;
        }
        if ($format) {
            return $this->priceCurrency->format($this->availableAmount);
        }

        return $this->availableAmount;
    }

    public function getPayoutAmount()
    {
        if ($this->payout === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->payout = $profitReport->getPayout($this->member->getMemberId());
        }
        return $this->payout;
    }

    /**
     * Get Life Time commission amount
     * @return float
     */
    public function getLifeTimeAmount()
    {
        if ($this->lifeTimeAmount === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->lifeTimeAmount = $profitReport->getLifeTimeCommissions($this->member->getMemberId());
        }
        return $this->priceCurrency->format($this->lifeTimeAmount);
    }

    /**
     * Get Life Time commission amount
     * @return float
     */
    public function getPendingAmount()
    {

        $period = $this->config->getHoldPeriod();
        if ($this->pendingAmount === null && $period > 0) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->pendingAmount = $profitReport->getPendingCommission($this->member->getMemberId(), $period);
        }

        return $this->priceCurrency->format($this->pendingAmount);
    }

    public function getRequestUrl()
    {
        return $this->getUrl(
            'salesrep/dashboard/request',
            [
                '_current' => true
            ]
        );
    }

    public function checkPermission()
    {
        $user = $this->session->getUser();
        $permission = $this->policyInterface->isAllowed($user->getRole()->getId(), 'BrainActs_SalesRepresentative::sales_representative_dashboard_request');

        return $permission;
    }
}
