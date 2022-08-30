<?php

namespace Designnbuy\Template\Controller\Adminhtml\Layout;

/**
 * New Template Action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class NewAction extends \Designnbuy\Template\Controller\Adminhtml\Template
{
    /**
     * Dispatch request
     */
    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();

        return $resultForward->forward('edit');
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
//        return $this->_authorization->isAllowed('Designnbuy_Template::save');
    }
}
