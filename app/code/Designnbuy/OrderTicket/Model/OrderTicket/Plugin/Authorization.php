<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\OrderTicket\Model\OrderTicket\Plugin;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Designnbuy\OrderTicket\Model\OrderTicket;
use Designnbuy\OrderTicket\Model\OrderTicketRepository;

class Authorization
{
    /**
     * @var UserContextInterface
     */
    protected $userContext;

    /**
     * @param UserContextInterface $userContext
     */
    public function __construct(
        UserContextInterface $userContext
    ) {
        $this->userContext = $userContext;
    }

    /**
     * Check if orderticket is allowed
     *
     * @param OrderTicketRepository $subject
     * @param OrderTicket $orderticketModel
     * @return OrderTicket
     * @throws
     * @SuppressWarnings(PHPMD.UnusedFoorderticketlParameter)
     */
    public function afterGet(
        OrderTicketRepository $subject,
        OrderTicket $orderticketModel
    ) {
        if (!$this->isAllowed($orderticketModel)) {
            throw NoSuchEntityException::singleField('orderticketId', $orderticketModel->getCustomerId());
        }
        return $orderticketModel;
    }

    /**
     * Check whether orderticket is allowed for current user context
     *
     * @param OrderTicket $orderticketModel
     * @return bool
     */
    protected function isAllowed(OrderTicket $orderticketModel)
    {
        return $this->userContext->getUserType() == UserContextInterface::USER_TYPE_CUSTOMER
            ? $orderticketModel->getCustomerId() == $this->userContext->getUserId()
            : true;
    }
}
