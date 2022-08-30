<?php

namespace Designnbuy\Orderattachment\Plugin\Sales\Order\Item;

class Attachment {
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['attachment'] = "col-attachment";

        $result->setcolumns($columns);
        return $result;
    }
}