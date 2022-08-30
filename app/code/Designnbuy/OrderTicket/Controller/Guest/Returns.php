<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Guest;

use Designnbuy\OrderTicket\Model\OrderTicket;

class Returns extends \Designnbuy\OrderTicket\Controller\Guest
{
    /**
     * View all returns
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->orderticketHelper->isEnabled()) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        $result = $this->salesGuestHelper->loadValidOrder($this->_request);
        if ($result instanceof \Magento\Framework\Controller\ResultInterface) {
            return $result;
        }

        $resultPage = $this->resultPageFactory->create();
        $this->_objectManager->get('Magento\Sales\Helper\Guest')->getBreadcrumbs($resultPage);
        return $resultPage;
    }
}
