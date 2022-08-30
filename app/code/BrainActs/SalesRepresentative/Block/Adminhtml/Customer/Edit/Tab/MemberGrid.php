<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Customer\Api\CustomerRepositoryInterface;

/**
 * Class MemberGrid
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class MemberGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Member
     */
    private $memberFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member
     */
    private $memberResourceFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * MemberGrid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory,
        \Magento\Framework\Registry $coreRegistry,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {

        $this->memberFactory = $memberFactory;
        $this->coreRegistry = $coreRegistry;
        $this->customerRepository = $customerRepository;
        $this->memberResourceFactory = $memberResourceFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('salesrep_customer_member');
        $this->setDefaultSort('member_id');
        $this->setUseAjax(true);
    }

    /**
     * @return \Magento\Customer\Api\Data\CustomerInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomer()
    {
        $customerId = $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return $this->customerRepository->getById($customerId);
    }

    /**
     * @param Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_member') {
            $memberIds = $this->_getSelectedMember();
            if (empty($memberIds)) {
                $memberIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('member_id', ['in' => $memberIds]);
            } elseif (!empty($memberIds)) {
                $this->getCollection()->addFieldToFilter('member_id', ['nin' => $memberIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        if ($this->getCustomer()->getId()) {
            $this->setDefaultFilter(['in_member' => 1]);
        }

        $collection = $this->memberFactory->create()->getCollection();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()
    {

        $this->addColumn(
            'in_member',
            [
                'type' => 'checkbox',
                'name' => 'in_member',
                'values' => $this->_getSelectedMember(),
                'index' => 'member_id',
                'header_css_class' => 'col-select col-massaction',
                'column_css_class' => 'col-select col-massaction'
            ]
        );

        $this->addColumn(
            'member_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'member_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('firstname', ['header' => __('First Name'), 'index' => 'firstname']);
        $this->addColumn('lastname', ['header' => __('Last Name'), 'index' => 'lastname']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $url = $this->getUrl('salesrep/customer/representative', ['_current' => true]);
        return $url;
    }

    /**
     * @return array|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function _getSelectedMember()
    {
        $members = $this->getRequest()->getPost('selected_members', []);
        if (count($members) == 0) {
            $customer = $this->getCustomer();

            $members = $this->getMembersByCustomer($customer->getId());
            return $members;
        }

        return $members;
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
}
