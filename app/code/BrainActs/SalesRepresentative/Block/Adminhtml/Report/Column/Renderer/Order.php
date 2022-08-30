<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;

/**
 * Class Order
 * @author BrainActs Core Team <support@brainacts.com>
 */
class Order extends AbstractRenderer
{

    /**
     * Renders grid column
     *
     * @param   DataObject $row
     * @return  string
     */
    public function render(DataObject $row)
    {
        $url = $this->getUrl('sales/order/view', [
            'order_id' => $row->getData('order_id')
        ]);

        $html = '<a target="_blank" href="' . $url . '" ';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '">';
        $html .= $row->getData('increment_order_id');
        $html .= '</a>';

        return $html;
    }
}
