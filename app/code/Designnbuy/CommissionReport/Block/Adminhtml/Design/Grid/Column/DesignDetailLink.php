<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Design\Grid\Column;

use Magento\Framework\DataObject;

class DesignDetailLink extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
        $detailRowValue = $row->getItemId();
        $html = '';
        if($detailRowValue != ''){
            $html = '<a href="'.$this->getUrl("commissionreport/report/designdetail", array('id'=>$row->getItemId(), 'for'=>0)).'">View Details</a>';
        }
        return $html;
    }
}
