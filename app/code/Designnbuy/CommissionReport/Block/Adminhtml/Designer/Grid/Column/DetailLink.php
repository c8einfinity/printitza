<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Designer\Grid\Column;

use Magento\Framework\DataObject;

class DetailLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
        $detailRowValue = $row->getItemId();
        $html = '';
        if($detailRowValue != ''){
            $html = '<a href="'.$this->getUrl("commissionreport/report/designerdetail", array('id'=>$row->getUserId(), 'for'=>0)).'">View Details</a>';
        }
        return $html;
    }
}
