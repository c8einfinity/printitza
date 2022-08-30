<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */
namespace Drc\Storepickup\Controller\Adminhtml\Holidays;

use Magento\Framework\App\Filesystem\DirectoryList;

class Save extends \Drc\Storepickup\Controller\Adminhtml\Holidays
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
    protected $backendSession;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Drc\Storepickup\Model\HolidayFactory $holidayFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\Session $backendSession,
        \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->backendSession = $backendSession;
        parent::__construct($holidaysFactory, $registry, $resultRedirectFactory, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost('holidays');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $holidays = $this->initHolidays();
            $holidays->setData($data);
            $this->_eventManager->dispatch(
                'drc_storepickup_holidays_prepare_save',
                [
                    'holidays' => $holidays,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $holidays->save();                
                $this->messageManager->addSuccess(__('The Holidays has been saved.'));
                $this->backendSession->setDrcStorepickupHolidaysData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'drc_storepickup/*/edit',
                        [
                            'entity_id' => $holidays->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('drc_storepickup/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Holidays.'));
            }
            $this->_getSession()->setDrcStorepickupStorelocatorData($data);
            $resultRedirect->setPath(
                'drc_storepickup/*/edit',
                [
                    'entity_id' => $holidays->getEntityId(),
                    '_current' => true
                ]
            );

            return $resultRedirect;
        }
        $resultRedirect->setPath('drc_storepickup/*/');
        return $resultRedirect;
    }

}
