<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class Edit extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Edit ORDERTICKET
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $model = $this->_initModel();
            if (!$model->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('The wrong Order Ticket was requested.'));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_initAction();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(sprintf("#%s", $model->getIncrementId()));
        $this->_view->renderLayout();
    }
}
