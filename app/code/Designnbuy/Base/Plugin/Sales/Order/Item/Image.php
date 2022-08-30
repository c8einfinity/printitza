<?php

namespace Designnbuy\Base\Plugin\Sales\Order\Item;

class Image
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Adminhtml\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['image'] = "col-image";

        $result->setcolumns($columns);
        return $result;
    }
}