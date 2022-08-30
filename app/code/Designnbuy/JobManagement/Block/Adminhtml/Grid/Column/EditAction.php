<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Grid\Column;

/**
 * Admin Writer book grid statuses
 */
class EditAction extends \Magento\Backend\Block\Widget\Grid\Column
{

    public function getFrameCallback()
    {
        return [$this, 'setUpdateStatus'];
    }

    public function getSubmitActionUrl($entityId, $orderId)
    {
        return $this->getUrl('jobmanagement/*/edit', ['id' => $entityId, 'order_id' => $orderId]);
    }

    public function setUpdateStatus($value, $row, $column, $isExport)
    {
        $entityId = $row->getEntityId();
        $orderId = $row->getOrderId();

        $orderStatus = $row->getStatus();
        $cell = "";
        if($orderStatus == "canceled") {
            $cell .= '<span></span>';       
        }else{
            $cell .= '<a id="job_action_button" title="Edit" href="'.$this->getSubmitActionUrl($entityId, $orderId).'">';
            $cell .= '<span>Edit</span>';
            $cell .= '</a>';
        }        
        return $cell;
    }
}
?>
