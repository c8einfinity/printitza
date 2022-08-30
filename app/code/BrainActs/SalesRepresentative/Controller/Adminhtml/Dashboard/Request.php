<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Dashboard;

use Magento\Backend\Model\Auth\Session;
use Magento\Framework\Session\SessionManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class Request
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Request extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_dashboard_request';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\WithdrawalsFactory|null
     */
    private $withdrawalsFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface|null
     */
    private $withdrawalsRepository;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface|null
     */
    private $memberRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

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
     * Request constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory
     * @param \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface $withdrawalsRepository
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository
     * @param SessionManager $sessionManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory,
        \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface $withdrawalsRepository,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory $profitFactory,
        \BrainActs\SalesRepresentative\Model\Config $config
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->withdrawalsFactory = $withdrawalsFactory;
        $this->withdrawalsRepository = $withdrawalsRepository;
        $this->memberRepository = $memberRepository;
        $this->session = $sessionManager;
        $this->profitFactory = $profitFactory;
        $this->config = $config;
        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\Result\Json
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $amount = $this->getRequest()->getParam('amount', 0);
        $resultJson = $this->resultJsonFactory->create();
        $availableAmount = $this->getAvailableAmount();
        if ($amount && $amount <= $availableAmount) {
            try {
                $member = $this->getMember();

                /** @var  \BrainActs\SalesRepresentative\Model\Withdrawals $model */
                $withdrawal = $this->withdrawalsFactory->create();
                $data['amount'] = $amount;
                $data['member_id'] = $member->getId();
                $data['status'] = \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_PENDING;
                $withdrawal->setData($data);
                $this->withdrawalsRepository->save($withdrawal);
                $this->messageManager->addSuccess(__("Withdrawal has been requested."));
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__("You a not sales representative."));
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        } else {
            $this->messageManager->addErrorMessage('The requested amount is incorrect, please re-check.');
        }
        $data['redirect'] = $this->getUrl('salesrep/dashboard');
        return $resultJson->setData($data);
    }

    /**
     * Get Member Id by Admin User Id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getMember()
    {
        $adminId = $this->session->getUser()->getId();
        return $this->memberRepository->getByUserId($adminId);
    }

    private function getAvailableAmount()
    {
        if ($this->availableAmount === null) {
            $this->getLifeTimeAmount();
            $this->getPendingAmount();
            $this->getPayoutAmount();
            $this->availableAmount = $this->lifeTimeAmount - $this->pendingAmount - $this->payout;
        }
        return $this->availableAmount;
    }

    private function getPayoutAmount()
    {
        if ($this->payout === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->payout = $profitReport->getPayout(1);
        }
        return $this->payout;
    }

    /**
     * Get Life Time commission amount
     * @return float
     */
    private function getLifeTimeAmount()
    {
        if ($this->lifeTimeAmount === null) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->lifeTimeAmount = $profitReport->getLifeTimeCommissions(1);
        }
        return $this->lifeTimeAmount;
    }

    /**
     * Get Life Time commission amount
     * @return float
     */
    private function getPendingAmount()
    {

        $period = $this->config->getHoldPeriod();
        if ($this->pendingAmount === null && $period > 0) {
            /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\Profit $profitReport */
            $profitReport = $this->profitFactory->create();
            $this->pendingAmount = $profitReport->getPendingCommission(1, $period);
        }

        return $this->pendingAmount;
    }
}
