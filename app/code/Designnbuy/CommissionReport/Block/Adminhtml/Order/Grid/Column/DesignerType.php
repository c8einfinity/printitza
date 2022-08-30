<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Order\Grid\Column;
use Magento\Framework\DataObject;

class DesignerType extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    public function render(DataObject $row)
    {
        if($row->getUserType() == 2):
            return "Designer";
        elseif($row->getUserType() == 1):
            return "Reseller";
        else:
            return "";
        endif;
    }
}
