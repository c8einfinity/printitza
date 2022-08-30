<?php

namespace Designnbuy\CommissionReport\Block\Adminhtml\Order\Grid\Column;
use Magento\Framework\DataObject;

class OrderStatus extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{////Magento\Sales\Ui\Component\Listing\Column\Status\Options

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Sales\Ui\Component\Listing\Column\Status\Options $orderOptions,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_orderOptions = $orderOptions;
    }

    public function render(DataObject $row)
    {
        if($row->getUserType() == 2):
            return "Designer";
        elseif($row->getUserType() == 1):
            return "Reseller";
        else:
            return "";
        endif;
    }
}
