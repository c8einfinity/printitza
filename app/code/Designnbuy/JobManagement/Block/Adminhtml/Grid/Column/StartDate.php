<?php

namespace Designnbuy\JobManagement\Block\Adminhtml\Grid\Column;

/**
 * Admin Writer book grid statuses
 */
class StartDate extends \Magento\Backend\Block\Widget\Grid\Column
{
    
    public function getFrameCallback()
    {
        return [$this, 'selectStartDate'];
    }

    public function selectStartDate($value, $row, $column, $isExport)
    {
        $cell = "";
        $cell .= '<input type="text" class="input-text" id="start-date" name="start-date" aria-required="true" />';
        return $cell;
    }
} ?>
<script>
     require([
          "jquery",
          "mage/calendar"
     ], function($){
         $("#start-date").calendar({
              buttonText:"<?php echo __('Select Date') ?>",
         });
       });
</script>