<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard\Withdrawals;

use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class WithdrawalsGrid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    private $withdrawalsFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface|null
     */
    private $memberRepository;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $session;

    /**
     * WithdrawalsGrid constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Session\SessionManager $sessionManager
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository
     * @param array $data
     */
    public function __construct(//@codingStandardsIgnoreLine
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Session\SessionManager $sessionManager,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        array $data = []
    ) {

        $this->withdrawalsFactory = $withdrawalsFactory;
        $this->coreRegistry = $coreRegistry;
        $this->session = $sessionManager;
        $this->memberRepository = $memberRepository;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->setId('salesrep_member_withdrawal');
        $this->setDefaultSort('withdrawal_id');
        $this->setUseAjax(true);
    }

    /**
     * Get Member Id by Admin User Id
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getMember()
    {
        $adminId = $this->session->getUser()->getId();
        return $this->memberRepository->getByUserId($adminId);
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)//@codingStandardsIgnoreLine
    {
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()//@codingStandardsIgnoreLine
    {
        $member = $this->getMember();
        $collection = $this->withdrawalsFactory->create()->getCollection();

        $this->setCollection($collection);

        $this->getCollection()->addFieldToFilter('member_id', ['eq' => $member->getId()]);

        return parent::_prepareCollection();
    }

    /**
     * @return $this|Extended
     * @throws \Exception
     */
    protected function _prepareColumns()//@codingStandardsIgnoreLine
    {
        $this->addColumn(
            'withdrawal_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'withdrawal_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('amount', [
            'header' => __('Amount'),
            'type' => 'currency',
            'index' => 'amount'
        ]);

        $this->addColumn('creation_time', [
            'header' => __('Date'),
            'index' => 'creation_time',
            'gmtoffset' => true,
            'type' => 'datetime'
        ]);
        $this->addColumn('status', [
            'header' => __('Status'),
            'index' => 'status',
            'filter'=>false,
            'renderer' => \BrainActs\SalesRepresentative\Block\Adminhtml\Dashboard\Withdrawals\Status::class
        ]);
        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        $url = $this->getUrl('salesrep/dashboard/withdrawals', ['_current' => true]);
        return $url;
    }
}
