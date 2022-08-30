<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model;

/**
 * Class Links
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 * @method getOrderId()
 * @method getMemberId()
 * @method getRule()
 */
class Links extends \Magento\Framework\Model\AbstractModel
{

    const RULE_TYPE_CUSTOMER = 1;
    const RULE_TYPE_PRODUCT = 2;
    const RULE_TYPE_REGION = 3;
    const RULE_TYPE_ORDER = 4;
    const RULE_TYPE_ORDER_AUTOASSIGN = 5;

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()//@codingStandardsIgnoreLine
    {
        parent::_construct();
        $this->_init(\BrainActs\SalesRepresentative\Model\ResourceModel\Links::class);
    }
}
