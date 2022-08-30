<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column;
use Magento\Framework\DataObject;

class ProductCommissionEarned extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper
    ){
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->pricingHelper = $pricingHelper;
    }

    public function getProductEarnedTotal($productId)
    {
        $commissionCollectionFactory = $this->commissionCollectionFactory->create();
        $commissionEarned = $commissionCollectionFactory
                        ->addFieldToFilter('item_purchased_id', $productId)
                        ->addFieldToFilter('user_type', 1)
                        ->getColumnValues('commission_amount');

        $totalEarnedCommission =  array_sum($commissionEarned);
        return $this->pricingHelper->currency($totalEarnedCommission, true, false);
    }

    public function render(DataObject $row)
    {
        return $this->getProductEarnedTotal($row->getItemPurchasedId());
    }
}
