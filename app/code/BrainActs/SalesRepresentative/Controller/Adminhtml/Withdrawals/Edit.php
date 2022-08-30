<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Withdrawals;

/**
 * Class Edit
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Edit extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_withdrawals_save';

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var BrainActs\SalesRepresentative\Model\WithdrawalsFactory
     */
    private $withdrawalsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->withdrawalsFactory = $withdrawalsFactory;
        parent::__construct($context);
    }

    /**
     * Edit member
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('withdrawal_id');
        $model = $this->withdrawalsFactory->create();

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This withdrawals no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('salesrep_withdrawals', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('BrainActs_SalesRepresentative::sales_representative_withdrawals');
        $resultPage->getConfig()->getTitle()->prepend(__('Withdrawals'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Withdrawal') : __('New Withdrawal'));
        return $resultPage;
    }
}
