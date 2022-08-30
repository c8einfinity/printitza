<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Grid\Column;

/**
 * Admin Writer book grid statuses
 */
class UpdateAction extends \Magento\Backend\Block\Widget\Grid\Column
{

    public function getFrameCallback()
    {
        return [$this, 'setUpdateStatus'];
    }

    public function getSubmitActionUrl($entityId, $itemId, $orderId, $productId, $workflowId)
    {
        return $this->getUrl('jobmanagement/*/updatejob', ['entity_id' => $entityId, 'item_id' => $itemId, 'order_id' => $orderId, 'product_id' => $productId, 'workflow_id' => $workflowId]);  
    }

    public function setUpdateStatus($value, $row, $column, $isExport)
    {
        $entityId = $row->getEntityId();
        $itemId = $row->getItemId();
        $orderId = $row->getOrderId();
        $productId = $row->getProductId();
        $workflowId = $row->getWorkflowId();
        $cell = "";
        $cell .= '<a id="job_action_button" title="Update" class="action-default scalable action-save action-secondary" href="'.$this->getSubmitActionUrl($entityId, $itemId, $orderId, $productId, $workflowId).'">';
        $cell .= '<span>Update</span>';
        $cell .= '</button>';
        return $cell;
    }
}
?>
