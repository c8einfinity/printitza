<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Controller\Index;

/**
 * Template home page view
 */
class Index extends \Designnbuy\Template\App\Action\Action
{
    /**
     * View template homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->moduleEnabled()) {
            //return $this->_forwardNoroute();
        }

        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }

}
