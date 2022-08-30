<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Guest;

use Designnbuy\OrderTicket\Model\OrderTicket;

class View extends \Designnbuy\OrderTicket\Controller\Guest
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * View concrete orderticket
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result = $this->_loadValidOrderTicket();
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        $resultPage = $this->resultPageFactory->create();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($resultPage);
        $resultPage->getConfig()->getTitle()->set(
            __('Return #%1', $this->_coreRegistry->registry('current_orderticket')->getIncrementId())
        );
        return $resultPage;
    }
}
