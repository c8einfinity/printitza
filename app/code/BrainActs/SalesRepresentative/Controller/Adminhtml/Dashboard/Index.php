<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Dashboard;

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Dashboard
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_dashboard';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface|null
     */
    private $memberRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Index constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \Magento\Framework\Registry $coreRegistry
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->memberRepository = $memberRepository;
        $this->session = $sessionManager;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    private function initPage($resultPage)
    {
        $resultPage->setActiveMenu('BrainActs_SalesRepresentative::sales_representative_dashboard');
        return $resultPage;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $member = $this->getMember();
            $member->setProductsReadonly(true);
            $this->coreRegistry->register('salesrep_member', $member);
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage(__("You a not sales representative."));
            return $this->resultRedirectFactory->create()->setPath('admin/dashboard');
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->getConfig()->getTitle()->prepend(__('Sales Representative Dashboard'));
        return $resultPage;
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
