<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\Order\View;

/**
 * Additional buttons on order view page
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Buttons extends \Magento\Sales\Block\Adminhtml\Order\View
{
    const CREATE_ORDERTICKET_BUTTON_DEFAULT_SORT_ORDER = 35;

    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketData = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Sales\Helper\Reorder $reorderHelper
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Sales\Helper\Reorder $reorderHelper,
        \Designnbuy\OrderTicket\Helper\Data $orderticketData,
        array $data = []
    ) {
        $this->_orderticketData = $orderticketData;
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $data);
    }

    /**
     * Add button to Shopping Cart Management etc.
     *
     * @return $this
     */
    public function addButtons()
    {
        if ($this->_isAllowedAction('Designnbuy_OrderTicket::create')) {
            if ($this->_isCreateOrderTicketButtonRequired()) {
                $parentBlock = $this->getParentBlock();
                $buttonUrl = $this->_urlBuilder->getUrl(
                    'adminhtml/orderticket/new',
                    ['order_id' => $parentBlock->getOrderId()]
                );

                $this->getToolbar()->addChild(
                    'create_orderticket',
                    'Magento\Backend\Block\Widget\Button',
                    ['label' => __('Create Order Ticket'), 'onclick' => 'setLocation(\'' . $buttonUrl . '\')']
                );
            }
        }
        return $this;
    }

    /**
     * Check if 'Create ORDERTICKET' button has to be displayed
     *
     * @return boolean
     */
    protected function _isCreateOrderTicketButtonRequired()
    {
        $parentBlock = $this->getParentBlock();
        return $parentBlock instanceof \Magento\Backend\Block\Template &&
            $parentBlock->getOrderId() &&
            $this->_orderticketData->canCreateOrderTicket(
                $parentBlock->getOrder(),
                true
            );
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
