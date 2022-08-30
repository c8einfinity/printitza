<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Product\Grid\Column;

use Magento\Framework\DataObject;

class ProductDetailLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
        $detailRowValue = $row->getItemId();
        $html = '';
        if($detailRowValue != ''){
            $html = '<a href="'.$this->getUrl("commissionreport/report/productdetail", array('id'=>$row->getItemPurchasedId(), 'for'=>0)).'">View Details</a>';
        }
        return $html;
    }
}
