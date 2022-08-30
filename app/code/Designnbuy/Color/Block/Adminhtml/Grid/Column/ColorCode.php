<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Adminhtml\Grid\Column;

/**
 * Admin clipart grid statuses 
 */
class ColorCode extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * Add to column decorated status
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return [$this, 'renderColorCode'];
    }

    /**
     * Decorate color_code column values
     *
     * @param string $value
     * @param  \Magento\Framework\Model\AbstractModel $row
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function renderColorCode($value, $row, $column, $isExport)
    {
        $cell = '';
        if ($row->getColorCode()) {
            $cell = '<span style="text-align: center;text-transform:uppercase; display:block; background: ' . $row->getColorCode() . '"><span>' . $row->getColorCode() . '</span></span>';
        }
        return $cell;
    }
}
