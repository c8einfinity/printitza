<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Design\Grid\Column;

use Magento\Framework\DataObject;

class DesignQtySold extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function __construct(
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory
    ){
        $this->commissionCollectionFactory = $commissionCollectionFactory;
    }

    public function getDesignSoldQty($designId)
    {
        $commissionCollectionFactory = $this->commissionCollectionFactory->create();
        $designSoldQtyCollection = $commissionCollectionFactory->addFieldToFilter('item_id', $designId)->getColumnValues('item_qty');
        return array_sum($designSoldQtyCollection);
    }

    public function render(DataObject $row)
    {
        return $this->getDesignSoldQty($row->getItemId());
    }
}
