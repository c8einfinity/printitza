<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Order;

/**
 * "Returns" link
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Link extends \Magento\Sales\Block\Order\Link
{
    /**
     * OrderTicket data
     *
     * @var \Designnbuy\OrderTicket\Helper\Data
     */
    protected $_orderticketHelper;

    /**
     * OrderTicket grid collection
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\DefaultPathInterface $defaultPath
     * @param \Magento\Framework\Registry $registry
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Designnbuy\OrderTicket\Helper\Data $orderticketHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\DefaultPathInterface $defaultPath,
        \Magento\Framework\Registry $registry,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Designnbuy\OrderTicket\Helper\Data $orderticketHelper,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_orderticketHelper = $orderticketHelper;
        parent::__construct($context, $defaultPath, $registry, $data);
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isOrderTicketAviable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get is link aviable
     *
     * @return bool
     */
    protected function _isOrderTicketAviable()
    {
        if ($this->_orderticketHelper->isEnabled()) {
            /** @var $collection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
            $collection = $this->_collectionFactory->create();
            $returns = $collection->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'order_id',
                $this->_registry->registry('current_order')->getId()
            )->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}
