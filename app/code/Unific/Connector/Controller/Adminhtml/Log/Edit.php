<?php

namespace Unific\Connector\Controller\Adminhtml\Log;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    protected $logFactory;

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Unific\Connector\Model\Audit\LogFactory $logFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Unific\Connector\Model\Audit\LogFactory $logFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);

        $this->coreRegistry = $registry;
        $this->logFactory = $logFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $logModel = $this->logFactory->create();

        $id = $this->getRequest()->getParam('id');
        if ($id > 0) {
            $logModel->load($id);
        }

        $this->coreRegistry->register('_unific_connector_log', $logModel);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Unific_Connector::log');
        $resultPage->getConfig()->getTitle()->prepend(__('Connector Log'));

        return $resultPage;
    }
}
