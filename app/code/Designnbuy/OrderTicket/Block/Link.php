<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block;

/**
 * ORDERTICKET Return Block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Link extends \Magento\Framework\View\Element\Html\Link\Current
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketHelper = null;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Designnbuy\OrderTicket\Helper\Data $orderticketHelper,
        array $data = []
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_orderticketHelper = $orderticketHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
     */
    protected function _toHtml()
    {
        if ($this->_orderticketHelper->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
