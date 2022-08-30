<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard\Assigned;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;

/**
 * Class AssignCustomers
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class AssignCustomers extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'dashboard/assign_customers.phtml';//@codingStandardsIgnoreLine

    /**
     * @var \BrainActs\SalesRepresentative\Block\Adminhtml\Order\View\Tab\Representative
     */
    public $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
    
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard\Assigned\Tab\Customer::class,
                'salesrep.member.customer.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getCustomersJson()
    {
        $customers = $this->getMember()->getCustomers();
        if (!empty($customers)) {
            return $this->jsonEncoder->encode($customers);
        }
        return '{}';
    }

    /**
     * Retrieve current category instance
     *
     * @return array|null
     */
    public function getMember()
    {
        return $this->registry->registry('salesrep_member');
    }
}
