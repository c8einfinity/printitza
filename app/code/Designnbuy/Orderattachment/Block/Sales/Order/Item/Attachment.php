<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Designnbuy\Orderattachment\Block\Sales\Order\Item;

use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Order item render block
 *
 * @api
 * @since 100.0.2
 */
class Attachment extends \Magento\Framework\View\Element\Template
{
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Catalog\Model\Product\OptionFactory
     */
    protected $_productOptionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Designnbuy\Orderattachment\Helper\Data $orderAttchmentHelper,
        array $data = []
    ) {
        $this->string = $string;
        $this->_productOptionFactory = $productOptionFactory;
        $this->_orderAttchmentHelper = $orderAttchmentHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * @return array|null
     */
    public function getItem()
    {
        $parent = $this->getParentBlock();
        if ($parent) {
            $item = $parent->getItem();
        }
        return $item;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getOrderItem()->getOrder();
    }



    /**
     * @return array|null
     */
    public function getOrderItem()
    {
        if ($this->getItem() instanceof \Magento\Sales\Model\Order\Item) {
            return $this->getItem();
        } else {
            return $this->getItem()->getOrderItem();
        }
    }

    public function getAttachments()
    {
        $_item = $this->getItem();
        $productOptions = $_item->getProductOptions();
        $itemAttachments = [];
        if(isset($productOptions['info_buyRequest']) && isset($productOptions['info_buyRequest']['attachment'])){
            $itemAttachments = $productOptions['info_buyRequest']['attachment'];
        }

        return $itemAttachments;
    }

    public function getImageUrlPath()
    {
        return $this->_orderAttchmentHelper->getImageUrlPath();
    }

}
