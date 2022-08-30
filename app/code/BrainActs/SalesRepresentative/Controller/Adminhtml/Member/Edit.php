<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Member;

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
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member_save';

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
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProductsFactory
     */
    private $productsFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProductsFactory $productsFactory
    ) {

        $this->resultPageFactory = $resultPageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->productsFactory = $productsFactory;
        parent::__construct($context);
    }

    /**
     * Edit member
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
//        $report = $this->productsFactory->create();
//        $report->aggregate(100, 51, []);
//        die('1');
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('member_id');
        $model = $this->_objectManager->create('BrainActs\SalesRepresentative\Model\Member');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This member no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('salesrep_member', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $resultPage->setActiveMenu('BrainActs_SalesRepresentative::sales_representative_member');
        $resultPage->getConfig()->getTitle()->prepend(__('Members'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Member') : __('New Member'));

        return $resultPage;
    }
}
