<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket;

class NewOrderTicket extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_orderticketData = $orderticketData;
        parent::__construct($context, $data);
    }

    /**
     * Initialize ORDERTICKET new page. Set management buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_OrderTicket';
        $this->_blockGroup = 'Designnbuy_OrderTicket';

        parent::_construct();

        $this->buttonList->update('reset', 'label', __('Cancel'));
        $this->buttonList->update('reset', 'class', 'cancel');

        $link = $this->getUrl('adminhtml/*/');
        $order = $this->_coreRegistry->registry('current_order');

        if ($order && $order->getId()) {
            $orderId = $order->getId();
            $referer = $this->getRequest()->getServer('HTTP_REFERER');

            if (strpos($referer, 'customer') !== false) {
                $link = $this->getUrl(
                    'customer/index/edit/',
                    ['id' => $order->getCustomerId(), 'active_tab' => 'orders']
                );
            }
        } else {
            return;
        }

        $this->buttonList->update('reset', 'onclick', "setLocation('" . $link . "')");
        
    }

    /**
     * Get header text for ORDERTICKET edit page
     *
     * @return string
     */
    public function getHeaderText()
    {
        return $this->getLayout()->createBlock('Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Create\Header')->toHtml();
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl(
            'adminhtml/*/save',
            ['order_id' => $this->_coreRegistry->registry('current_order')->getId()]
        );
    }
}
