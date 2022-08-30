<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer;

use BrainActs\SalesRepresentative\Model\Links;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Rule
 * @author BrainActs Core Team <support@brainacts.com>
 */
class Rule extends AbstractRenderer
{

    /**
     * Renders grid column
     *
     * @param   DataObject $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $type = $row->getData('rule_type');


        switch ($type) {
            case Links::RULE_TYPE_CUSTOMER:
                return __('Customer Rule');
            break;
            case Links::RULE_TYPE_ORDER:
                return __('Manual Assignment');
                break;

            case Links::RULE_TYPE_ORDER_AUTOASSIGN:
                return __('Automatic Assignment');
                break;

            case Links::RULE_TYPE_PRODUCT:
                return __('Product Rule');
                break;
        }

        return '';
    }
}
