<?php

namespace Designnbuy\Workflow\Plugin\Sales\Order\Item;

class Status
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['workflowstatus'] = "col-workflowstatus";

        $result->setcolumns($columns);
        return $result;
    }
}