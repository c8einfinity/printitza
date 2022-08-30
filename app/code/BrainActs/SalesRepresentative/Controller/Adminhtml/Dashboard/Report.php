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
 * Class Report
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Report extends \Magento\Backend\App\Action
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

    /**
     * @var \BrainActs\SalesRepresentative\Model\GraphReportFactory
     */
    public $graphReportFactory;

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
        \BrainActs\SalesRepresentative\Model\Config $config,
        \BrainActs\SalesRepresentative\Model\GraphReportFactory $graphReportFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->withdrawalsFactory = $withdrawalsFactory;
        $this->withdrawalsRepository = $withdrawalsRepository;
        $this->memberRepository = $memberRepository;
        $this->session = $sessionManager;
        $this->profitFactory = $profitFactory;
        $this->config = $config;
        $this->graphReportFactory = $graphReportFactory;
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
        $period = $this->getRequest()->getParam('period', '7d');
        $resultJson = $this->resultJsonFactory->create();
        $member = $this->getMember();

        $report = $this->graphReportFactory->create()->prepareReportData($period, $member);

        return $resultJson->setData($report);
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
}
