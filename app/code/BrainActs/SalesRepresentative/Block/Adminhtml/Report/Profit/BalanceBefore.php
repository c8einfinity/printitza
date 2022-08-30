<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Profit;

/**
 * Class BalanceBefore
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class BalanceBefore extends \Magento\Framework\View\Element\Template
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
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;


    /**
     * BalanceBefore constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory $profitFactory
     * @param \BrainActs\SalesRepresentative\Model\Config $config
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository
     * @param \Magento\Framework\Authorization\PolicyInterface $policyInterface
     * @param \Magento\Backend\Helper\Data $helperData
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
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
        \Magento\Backend\Helper\Data $helperData,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        array $data = []
    ) {

        $this->priceCurrency = $priceCurrency;
        $this->profitFactory = $profitFactory;
        $this->config = $config;
        $this->session = $sessionManager;
        $this->memberRepository = $memberRepository;
        $this->policyInterface = $policyInterface;
        $this->dateFilter = $dateFilter;
        parent::__construct($context, $data);
    }

    private function getMember()
    {
        $adminId = $this->session->getUser()->getId();
        $this->member = $this->memberRepository->getByUserId($adminId);
    }

    public function prepareFilterString($filterString)
    {
        $data = [];
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive(
            $data,
            // @codingStandardsIgnoreStart
            /**
             * Decodes URL-encoded string and trims whitespaces from the beginning and end of a string
             *
             * @param string $value
             */
            // @codingStandardsIgnoreEnd
            function (&$value) {
                $value = trim(rawurldecode($value));
            }
        );
        return $data;
    }

    private function initParams()
    {

        $requestData = $this->prepareFilterString($this->getRequest()->getParam('filter'));
        $inputFilter = new \Zend_Filter_Input(
            ['from' => $this->dateFilter, 'to' => $this->dateFilter],
            [],
            $requestData
        );

        $requestData = $inputFilter->getUnescaped();
        $requestData['store_ids'] = $this->getRequest()->getParam('store_ids');
        $params = new \Magento\Framework\DataObject();

        foreach ($requestData as $key => $value) {
            if (!empty($value)) {
                $params->setData($key, $value);
            }
        }

        return $params;
    }

    public function getAvailableAmount($format = true)
    {

        $this->getMember();
        $params = $this->initParams();

        if ($this->availableAmount === null) {
            $this->getLifeTimeAmount($params);
            $this->getPayoutAmount($params);
            $this->availableAmount = $this->lifeTimeAmount - $this->payout;
        }
        if ($format) {
            return $this->priceCurrency->format($this->availableAmount);
        }

        return $this->availableAmount;
    }

    public function getPayoutAmount($params)
    {
        if ($this->payout === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->payout = $profitReport->getPayoutCompleted($params->getMemberId(), $params->getFrom());
        }
        return $this->payout;
    }

    /**
     * Get Life Time commission amount
     * @param $params
     * @return float
     */
    public function getLifeTimeAmount($params)
    {
        if ($this->lifeTimeAmount === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->lifeTimeAmount = $profitReport->getLifeTimeCommissions($params->getMemberId(), $params->getFrom());
        }
        return $this->priceCurrency->format($this->lifeTimeAmount);
    }
}
