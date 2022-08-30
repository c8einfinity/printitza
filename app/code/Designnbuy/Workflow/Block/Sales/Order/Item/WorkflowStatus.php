<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Designnbuy\Workflow\Block\Sales\Order\Item;

use Magento\Sales\Model\Order\CreditMemo\Item as CreditMemoItem;
use Magento\Sales\Model\Order\Invoice\Item as InvoiceItem;
use Magento\Sales\Model\Order\Item as OrderItem;

/**
 * Order item render block
 *
 * @api
 * @since 100.0.2
 */
class WorkflowStatus extends \Magento\Framework\View\Element\Template
{
    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Designnbuy\Workflow\Model\StatusFactory
     */
    protected $statusFactory;


    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Designnbuy\Workflow\Model\StatusFactory $statusFactory,
        array $data = []
    ) {
        $this->string = $string;
        $this->statusFactory = $statusFactory;
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

    public function getWorkflowStatus()
    {
        $statusTitle = '';
        $isDisplayUserStatus = "";
        $_item = $this->getItem();
        if($_item->getWorkflowStatus() != '') {
            $statusFactory = $this->statusFactory->create();            
            $statusFactory->load($_item->getWorkflowStatus());            
            $isDisplayUserStatus = $statusFactory->getDisplayUserStatus();
            if($statusFactory->getUserStatusTitle() != '' && $isDisplayUserStatus == true){
                $statusTitle = $statusFactory->getUserStatusTitle();
            } else {
                $statusTitle = $statusFactory->getTitle();
            }
            //$statusTitle = $statusFactory->getUserStatusTitle();
        }
        return $statusTitle;

    }

}
