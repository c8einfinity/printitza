<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Controller\Adminhtml\Holidays;

class Edit extends \Drc\Storepickup\Controller\Adminhtml\Holidays
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * Page factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Result JSON factory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->backendSession    = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($holidaysFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * is action allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Drc_Storepickup::holidays');
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {        
        $id = $this->getRequest()->getParam('entity_id');
        /** @var \Drc\Storepickup\Model\Holidays $holidays */
        $holidays = $this->initHolidays();
        /** @var \Magento\Backend\Model\View\Result\Page|\Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Drc_Storepickup::holidays');
        $resultPage->getConfig()->getTitle()->set(__('Holidays'));
        if ($id) {
            $holidays->load($id);            
            if (!$holidays->getEntityId()) {
                $this->messageManager->addError(__('This Holidays no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath(
                    'drc_storepickup/*/edit',
                    [
                        'entity_id' => $holidays->getEntityId(),
                        '_current' => true
                    ]
                );
                return $resultRedirect;
            }
        }
        $title = $holidays->getEntityId() ? $holidays->getTitle() : __('New Holidays');        
        $resultPage->getConfig()->getTitle()->prepend($title);        
        $data = $this->backendSession->getData('drc_storepickup_holidays_data', true);

        if (!empty($data)) {
            $holidays->setData($data);
        }
        return $resultPage;
    }
}
