<?php

namespace Designnbuy\Designidea\Controller\Adminhtml\Designidea;

/**
 * New Designidea Action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
class NewAction extends \Designnbuy\Designidea\Controller\Adminhtml\Designidea
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
//        return $this->_authorization->isAllowed('Designnbuy_Designidea::save');
    }
}
