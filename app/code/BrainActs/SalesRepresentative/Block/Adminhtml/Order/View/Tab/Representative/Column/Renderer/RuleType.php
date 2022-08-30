<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Order\View\Tab\Representative\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use BrainActs\SalesRepresentative\Model\LinksFactory;

class RuleType extends AbstractRenderer
{

    private $linkFactory;

    public function __construct(
        \Magento\Backend\Block\Context $context,
        LinksFactory $linkFactory,
        array $data = []
    ) {
    
        $this->linkFactory = $linkFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param DataObject $row
     * @return mixed
     */
    protected function _getValue(DataObject $row)
    {
        $model = $this->linkFactory->create();
        $collection = $model->getCollection();
        $collection->addFieldToFilter('order_id', $this->getRequest()->getParam('order_id'));
        $collection->addFieldToFilter('member_id', $row->getData('member_id'));

        $item = $collection->getFirstItem();

        $value = $item->getRule();

        switch ($value) {
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_CUSTOMER:
                $value = __('By Customer');
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_PRODUCT:
                $value = __('By Product');
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_REGION:
                $value = __('By Region');
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_ORDER:
                $value = __('By Order');
                break;
            case \BrainActs\SalesRepresentative\Model\Links::RULE_TYPE_ORDER_AUTOASSIGN:
                $value = __('By Order(Auto Assign)');
                break;
        }

        return $value;
    }
}
