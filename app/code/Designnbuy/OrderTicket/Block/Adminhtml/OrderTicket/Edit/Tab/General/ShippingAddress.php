<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General;

/**
 * Class ShippingAddress
 */
class ShippingAddress extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General\AbstractGeneral
{
    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        array $data = []
    ) {
        $this->addressRenderer = $addressRenderer;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Get order shipping address
     *
     * @return string|null
     */
    public function getOrderShippingAddress()
    {
        $address = $this->getOrder()->getShippingAddress();
        if ($address instanceof \Magento\Sales\Model\Order\Address) {
            return $this->addressRenderer->format($address, 'html');
        } else {
            return null;
        }
    }
}
