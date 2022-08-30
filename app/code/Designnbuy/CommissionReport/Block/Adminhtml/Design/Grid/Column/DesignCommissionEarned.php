<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Design\Grid\Column;
use Magento\Framework\DataObject;

class DesignCommissionEarned extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ){
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->pricingHelper = $pricingHelper;
    }

    public function getDesignEarnedTotal($designId)
    {
        $commissionCollectionFactory = $this->commissionCollectionFactory->create();
        $commissionEarned = $commissionCollectionFactory->addFieldToFilter('item_id', $designId)->getColumnValues('commission_amount');
        $totalEarnedCommission =  array_sum($commissionEarned);
        return $this->pricingHelper->currency($totalEarnedCommission, true, false);
    }

    public function render(DataObject $row)
    {
        return $this->getDesignEarnedTotal($row->getItemId());
    }
}
