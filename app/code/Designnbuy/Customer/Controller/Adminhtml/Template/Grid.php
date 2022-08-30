<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Template;

class Grid extends \Designnbuy\Customer\Controller\Adminhtml\Template
{
    /**
     * JSON Grid Action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $grid = $this->_view->getLayout()->createBlock('Designnbuy\Customer\Block\Adminhtml\Template\Grid')->toHtml();
        $this->getResponse()->setBody($grid);
    }
}
