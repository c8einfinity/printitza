<?php

namespace Designnbuy\Base\Plugin\Sales\Order\Item;

class Output
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['output'] = "col-output";

        $result->setcolumns($columns);
        return $result;
    }
}