<?php

namespace Designnbuy\Vendor\Plugin\Sales\Order\Item;

class Status
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['vendorstatus'] = "col-vendorstatus";

        $result->setcolumns($columns);
        return $result;
    }
}