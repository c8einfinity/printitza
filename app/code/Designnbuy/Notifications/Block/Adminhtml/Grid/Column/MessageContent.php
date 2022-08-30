<?php

namespace Designnbuy\Notifications\Block\Adminhtml\Grid\Column;

/**
 * Admin Notifications grid statuses
 */
class MessageContent extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */

    public function getFrameCallback()
    {
        return [$this, 'decorateMessageContent'];
    }

    public function decorateMessageContent($value, $row, $column, $isExport)
    {
        $cell = null;
        $cell .= '<b>'. $row->getTitle() .'</b><br />';
        $cell .= '<span style="text-transform: capitalize;">'. $row->getDescription().'</span>';
        return $cell;
    }
}
