<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column;

use Magento\Framework\DataObject;

class ProductQtySold extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory
    ){
        $this->commissionCollectionFactory = $commissionCollectionFactory;
    }

    public function getProductSoldQty($productId)
    {
        $commissionCollectionFactory = $this->commissionCollectionFactory->create();
        $commissionCollectionFactory->addFieldToFilter('user_type', 1);
        $productSoldQtyCollection = $commissionCollectionFactory->addFieldToFilter('item_purchased_id', $productId)->getColumnValues('item_qty');

        return array_sum($productSoldQtyCollection);
    }

    public function render(DataObject $row)
    {
        return $this->getProductSoldQty($row->getItemPurchasedId());
    }
}
