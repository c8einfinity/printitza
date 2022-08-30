<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Variable to store ORDERTICKET instance
     *
     * @var null|\Designnbuy\OrderTicket\Model\OrderTicket
     */
    protected $_orderticket = null;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize ORDERTICKET edit page. Set management buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_controller = 'adminhtml_OrderTicket';
        $this->_blockGroup = 'Designnbuy_OrderTicket';

        parent::_construct();

        if (!$this->getOrderTicket()) {
            return;
        }
        $statusIsClosed = in_array(
            $this->getOrderTicket()->getStatus(),
            [
                \Designnbuy\OrderTicket\Model\OrderTicket\Source\Status::STATE_CLOSED
            ]
        );

        if (!$statusIsClosed) {
            $this->buttonList->add(
                'save_and_edit_button',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                100
            );

            $this->buttonList->add(
                'close',
                [
                    'label' => __('Close'),
                    'class' => 'close',
                    'onclick' => 'confirmSetLocation(\'' . __(
                        'Are you sure you want to close this order ticket request?'
                    ) . '\', \'' . $this->getCloseUrl() . '\')'
                ]
            );
        } else {
            $this->buttonList->remove('save');
            $this->buttonList->remove('reset');
        }

        $this->buttonList->remove('delete');
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        $referer = $this->getRequest()->getServer('HTTP_REFERER');

        if (strpos($referer, 'sales_order') !== false) {
            return $this->getUrl(
                'sales/order/view/',
                ['order_id' => $this->getOrderTicket()->getOrderId(), 'active_tab' => 'order_orderticket']
            );
        } elseif (strpos($referer, 'customer') !== false) {
            return $this->getUrl(
                'customer/index/edit/',
                ['id' => $this->getOrderTicket()->getCustomerId(), 'active_tab' => 'customer_edit_tab_orderticket']
            );
        } else {
            return parent::getBackUrl();
        }
    }

    /**
     * Declare orderticket instance
     *
     * @return  \Designnbuy\OrderTicket\Model\Item
     */
    public function getOrderTicket()
    {
        if ($this->_orderticket === null) {
            $this->_orderticket = $this->_coreRegistry->registry('current_orderticket');
        }
        return $this->_orderticket;
    }

    /**
     * Get header text for ORDERTICKET edit page
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getHeaderText()
    {
        if ($this->getOrderTicket()->getId()) {
            return __('Order Ticket #%1 - %2', intval($this->getOrderTicket()->getIncrementId()), $this->getOrderTicket()->getStatusLabel());
        }

        return '';
    }

    /**
     * Get form action URL
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('adminhtml/*/save', ['orderticket_id' => $this->getOrderTicket()->getId()]);
    }

    /**
     * Get print ORDERTICKET action URL
     *
     * @return string
     */
    public function getPrintUrl()
    {
        return $this->getUrl('adminhtml/*/print', ['orderticket_id' => $this->getOrderTicket()->getId()]);
    }

    /**
     * Get close ORDERTICKET action URL
     *
     * @return string
     */
    public function getCloseUrl()
    {
        return $this->getUrl('adminhtml/*/close', ['entity_id' => $this->getOrderTicket()->getId()]);
    }
}
