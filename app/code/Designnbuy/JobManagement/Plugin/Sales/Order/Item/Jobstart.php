<?php

namespace Designnbuy\JobManagement\Plugin\Sales\Order\Item;

class Jobstart
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['jobstart'] = "col-jobstart";
        $result->setcolumns($columns);
        return $result;
    }
}