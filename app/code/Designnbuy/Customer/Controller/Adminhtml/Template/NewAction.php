<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Template;

class NewAction extends \Designnbuy\Customer\Controller\Adminhtml\Template
{
    /**
     * Create new Customer Template
     *
     * @return void
     */
    public function execute()
    {
        $this->_forward('edit');
    }
}
