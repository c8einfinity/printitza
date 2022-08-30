<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Model\OrderTicket;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\StateException;
use Designnbuy\OrderTicket\Helper\Data;
use Designnbuy\OrderTicket\Model\OrderTicket;

class PermissionChecker
{
    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @var Data
     */
    private $orderticketHelper;

    /**
     * @param UserContextInterface $userContext
     * @param Data $orderticketHelper
     */
    public function __construct(
        UserContextInterface $userContext,
        Data $orderticketHelper
    ) {
        $this->userContext = $userContext;
        $this->orderticketHelper = $orderticketHelper;
    }

    /**
     * Whether the user is the owner of the ORDERTICKET
     *
     * @param OrderTicket $orderticket
     * @return bool
     */
    public function isOrderTicketOwner(OrderTicket $orderticket)
    {
        return $this->isCustomerContext()
            ? $orderticket->getCustomerId() == $this->userContext->getUserId()
            : true;
    }

    /**
     * Verifies availability of orderticket for customer context
     *
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     */
    public function checkOrderTicketForCustomerContext()
    {
        if ($this->isCustomerContext() && !$this->orderticketHelper->isEnabled()) {
            throw new StateException(__('Unknown service'));
        }
        return true;
    }

    /**
     * Whether is the customer context
     *
     * @return bool
     */
    public function isCustomerContext()
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER;
    }
}
