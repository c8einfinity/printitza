<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class RateType
 * @author BrainActs Core Team <support@brainacts.com>
 */
class RateType extends AbstractRenderer
{

    /**
     * @param DataObject $row
     * @return mixed
     */
    protected function _getValue(DataObject $row)
    {
        $value = $row->getData($this->getColumn()->getIndex());

        switch ($value) {
            case 1:
                $value = __('Percent');
                break;
            case 2:
                $value = __('Fixed');
                break;
        }
        return $value;
    }
}
