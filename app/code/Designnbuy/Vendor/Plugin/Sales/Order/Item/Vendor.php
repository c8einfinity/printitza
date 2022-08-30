<?php

namespace Designnbuy\Vendor\Plugin\Sales\Order\Item;

class Vendor
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['vendor'] = "col-vendor";

        $result->setcolumns($columns);
        return $result;
    }
}