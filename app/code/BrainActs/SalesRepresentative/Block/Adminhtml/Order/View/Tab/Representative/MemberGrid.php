<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Order\View\Tab\Representative;

use BrainActs\SalesRepresentative\Block\Adminhtml\Order\View\Tab\Representative\Column\Renderer\RuleType;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

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
    public $coreRegistry = null;

    /**
     * @var \BrainActs\SalesRepresentative\Model\MemberFactory
     */
    public $memberFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ConfigFactory
     */
    private $configFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config|null
     */
    private $config = null;

    /**
     * MemberGrid constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory,
        \Magento\Framework\Registry $coreRegistry,
        \BrainActs\SalesRepresentative\Model\ConfigFactory $configFactory,
        array $data = []
    ) {

        $this->memberFactory = $memberFactory;
        $this->coreRegistry = $coreRegistry;
        $this->configFactory = $configFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct() //@codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->setId('salesrep_order_member');
        $this->setDefaultSort('member_id');
        $this->setUseAjax(true);
    }

    /**
     * @return array|null
     */
    public function getOrder()
    {
        return $this->coreRegistry->registry('current_order');
    }

    /**
     * Check if user allow to edit SR for order
     */
    public function isAllowEdit()
    {
        if ($this->config === null) {
            $this->config = $this->configFactory->create();
        }

        return $this->config->isAllowAssign();
    }

    /**
     * @param Column $column
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)//@codingStandardsIgnoreLine
    {
        // Set custom filter for in order flag
        if ($column->getId() == 'member_id') {
            $memberIds = $this->getSelectedMembers();
            if (empty($memberIds)) {
                $memberIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('main_table.member_id', ['in' => $memberIds]);
            } elseif (!empty($memberIds)) {
                $this->getCollection()->addFieldToFilter('main_table.member_id', ['nin' => $memberIds]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * @return Grid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()//@codingStandardsIgnoreLine
    {
        if ($this->getOrder()->getId()) {
            $this->setDefaultFilter(['member_id' => 1]);
        }

        $member = $this->memberFactory->create();
        $collection = $member->getCollection();

        $this->setCollection($collection);

        if (!$this->isAllowEdit()) {
            $memberIds = $this->getSelectedMembers();
            if (empty($memberIds)) {
                $memberIds = 0;
            }
            $this->getCollection()->addFieldToFilter('main_table.member_id', ['in' => $memberIds]);
        }

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws \Exception
     */
    protected function _prepareColumns()//@codingStandardsIgnoreLine
    {
        if ($this->isAllowEdit()) {
            $this->addColumn(
                'member_id',
                [
                    'type' => 'checkbox',
                    'name' => 'member_id',
                    'values' => $this->getSelectedMembers(),
                    'index' => 'member_id',
                    'header_css_class' => 'col-select col-massaction',
                    'column_css_class' => 'col-select col-massaction'
                ]
            );
        }

        $this->addColumn('firstname', [
            'header' => __('First Name'),
            'index' => 'firstname'
        ]);

        $this->addColumn('lastname', [
            'header' => __('Last Name'),
            'index' => 'lastname'
        ]);

        $this->addColumn('rule', [
            'header' => __('Rule'),
            'index' => 'rule',
            'renderer' => RuleType::class,
        ]);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $url = $this->getUrl('salesrep/member/order', ['_current' => true]);
        return $url;
    }

    /**
     * @return array
     */
    public function getSelectedMembers()
    {
//        $members = $this->getRequest()->getPost('selected_members');
//        if ($members === null) {
        $members = $this->getMembersByOrder($this->getOrder()->getId());
        if ($members === null) {
            $members = [];
        }
        return array_keys($members);
//        }
//
//        return $members;
    }

    public function getMembersByOrder($orderId)
    {
        $member = $this->memberFactory->create();
        $table = $member->getResource()->getTable('brainacts_salesrep_member_order');
        $collection = $member->getCollection();
        $collection->getSelect()->joinLeft(
            ['order_table' => $table],
            'main_table.member_id = order_table.member_id',
            []
        );
        $collection->addFieldToFilter('order_id', $orderId);
        $collection->getSelect()->group('member_id');
        $ids = [];
        foreach ($collection as $item) {
            $ids[$item->getMemberId()] = "";
        }
        return $ids;
    }
}
