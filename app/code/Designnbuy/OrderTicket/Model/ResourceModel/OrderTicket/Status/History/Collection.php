<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History;

use Magento\Sales\Model\ResourceModel\Collection\AbstractCollection;
use Designnbuy\OrderTicket\Api\Data\CommentSearchResultInterface;

/**
 * ORDERTICKET entity resource model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends AbstractCollection implements CommentSearchResultInterface
{
    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\OrderTicket\Model\OrderTicket\Status\History', 'Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Status\History');
    }
}
