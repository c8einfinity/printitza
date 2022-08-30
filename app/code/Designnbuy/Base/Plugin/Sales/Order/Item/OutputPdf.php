<?php

namespace Designnbuy\Base\Plugin\Sales\Order\Item;

class OutputPdf
{
    /**
     * @return array
     */
    public function afterGetItemRenderer(\Magento\Sales\Block\Items\AbstractItems $subject, $result)
    {
        $columns = $result->getcolumns();
        $columns['outputpdf'] = "col-outputpdf";

        $result->setcolumns($columns);
        return $result;
    }
}