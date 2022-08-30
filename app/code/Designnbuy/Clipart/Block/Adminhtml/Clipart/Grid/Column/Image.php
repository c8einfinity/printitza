<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Block\Adminhtml\Clipart\Grid\Column;

/**
 * Admin clipart grid statuses 
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'renderImage'];
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function renderImage($value, $row, $column, $isExport)
    {
        $cell = '';
        if ($row->getImage()) {
            $cell = '<image width="150" height="50" src ="' . $row->getImage() . '" alt="' . $row->getTitle() . '" >';
        }
        return $cell;
    }
}
