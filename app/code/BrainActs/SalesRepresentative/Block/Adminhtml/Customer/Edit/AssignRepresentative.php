<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Customer\Edit;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Class AssignRepresentative
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class AssignRepresentative extends Template implements TabInterface
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'customer/edit/assign_member.phtml';

    /**
     * @var \Magento\Catalog\Block\Adminhtml\Category\Tab\Product
     */
    private $blockGrid;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member
     */
    private $memberResourceFactory;

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
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory,
        array $data = []
    ) {

        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        $this->memberResourceFactory = $memberResourceFactory;
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
                \BrainActs\SalesRepresentative\Block\Adminhtml\Customer\Edit\Tab\MemberGrid::class,
                'salesrep.customer.member.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
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
    public function getMemberJson()
    {
        $customerId = $this->getCustomer();
        $members = $this->getMembersByCustomer($customerId);
        if (!empty($members)) {
            $members = array_flip($members);
            return $this->jsonEncoder->encode($members);
        }
        return '{}';
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        $customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $customerId;
    }

    /**
     * Return member ids that assigned to current customer
     * @param $customerId
     * @return array
     */
    private function getMembersByCustomer($customerId)
    {

        $resource = $this->memberResourceFactory->create();
        $ids = $resource->getMembersByCustomer($customerId);

        return $ids;
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

    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Representative');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Representative');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('salesrep/customer/representative', ['_current' => true]);
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
