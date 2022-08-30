<?php

namespace Designnbuy\Notifications\Block\Adminhtml\Grid\Column;

/**
 * Admin Notifications grid statuses
 */
class Types extends \Magento\Backend\Block\Widget\Grid\Column
{
    const TYPE_DESIGNER = 1;
        const TEXT_DESIGNER = 'Designer Request';

    const TYPE_RESELLER = 2;
        const TEXT_RESELLER = 'Reseller Request';

    const TYPE_DESIGN = 3;
        const TEXT_DESIGN = 'Publish Design';

    const TYPE_REPORT = 4;
        const TEXT_REPORT = 'Abuse Report';

    const TYPE_REDEEM = 5;
        const TEXT_REDEEM = 'Commission Redeem';

    const TYPE_UNPUBLISED = 6;
        const TEXT_UNPUBLISED = 'Design Unpublished';

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
        return [$this, 'decorateTypes'];
    }

    public function decorateTypes($value, $row, $column, $isExport)
    {
        $cell = null;
        if ($row->getType()) 
        {            
            switch ($row->getType()) {
                case self::TYPE_DESIGNER:
                    $cell = '<span class="grid-severity-notice">'. self::TEXT_DESIGNER .'</span>';
                    break;
                
                case self::TYPE_RESELLER:
                    $cell = '<span class="grid-severity-minor">'. self::TEXT_RESELLER .'</span>';
                    break;

                case self::TYPE_DESIGN:
                    $cell = '<span class="grid-severity-notice">'. self::TEXT_DESIGN .'</span>';
                    break;

                case self::TYPE_REPORT:
                    $cell = '<span class="grid-severity-major">'. self::TEXT_REPORT .'</span>';
                    break;

                case self::TYPE_REDEEM:
                    $cell = '<span class="grid-severity-major">'. self::TEXT_REDEEM .'</span>';
                    break;

                case self::TYPE_UNPUBLISED:
                    $cell = '<span class="grid-severity-critical">'. self::TEXT_UNPUBLISED .'</span>';
                    break;
                default:
                    break;
            }
        }
        return $cell;
    }
}
