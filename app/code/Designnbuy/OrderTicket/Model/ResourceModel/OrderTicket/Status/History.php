<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status;

use Designnbuy\OrderTicket\Model\Spi\CommentResourceInterface;

/**
 * ORDERTICKET entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb implements CommentResourceInterface
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_orderticket_status_history', 'entity_id');
    }
}
