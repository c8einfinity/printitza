<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Commission\Block\Commission;

use Designnbuy\Commission\Block\Commission\AbstractRedemption;
use Magento\Store\Model\ScopeInterface;

class Redemption extends AbstractRedemption
{
    protected function _prepareLayout()
    {
    	$this->pageConfig->getTitle()->set(__('Designer Commission Redemption'));
    	return $this; //parent::_prepareLayout();
    }

    public function getRedemptionPostUrl(){
    	return $this->getUrl('designer/commission/save');
    }
}