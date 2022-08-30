<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket;

/**
 * Admin ORDERTICKET create
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Create extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_OrderTicket';
        $this->_mode = 'create';
        $this->_blockGroup = 'Designnbuy_OrderTicket';

        parent::_construct();

        $this->setId('designnbuy_orderticket_orderticket_create');
        $this->removeButton('save');
        $this->removeButton('reset');
    }

    /**
     * Get header html
     *
     * @return string
     */
    public function getHeaderHtml()
    {
        return $this->getLayout()->createBlock('Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create\Header')->toHtml();
    }
}
