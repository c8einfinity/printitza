<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Model\ResourceModel;

/**
 * Color author resource model
 */
class Author extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     * Get tablename from config
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('admin_user', 'user_id');
    }

}
