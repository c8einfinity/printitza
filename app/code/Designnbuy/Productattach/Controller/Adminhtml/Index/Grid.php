<?php
namespace Designnbuy\Productattach\Controller\Adminhtml\Index;

/**
 * Class Grid
 * @package Designnbuy\Productattach\Controller\Adminhtml\Index
 */
class Grid extends \Magento\Customer\Controller\Adminhtml\Index
{
    /**
     * Customer grid action
     *
     * @return void
     */
    public function execute()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }
}
