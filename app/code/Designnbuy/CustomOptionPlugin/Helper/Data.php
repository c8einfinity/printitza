<?php
/**
 * Copyright © 2017 Designnbuy. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Designnbuy\CustomOptionPlugin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    /**
     * List Of DesignTool Type
     *
     * @return array
     */
    public function getDesigntoolTypeOptions()
    {
        return [
            ['value'=>'none', 'label'=> __('None')],
            ['value'=>'corner', 'label'=> __('Corner')],
            ['value'=>'background colors', 'label'=> __('Background Colors')],
            ['value'=>'sides', 'label'=> __('Sides')],  
			['value'=>'sizes', 'label'=> __('Sizes')],  
			['value'=>'quantity', 'label'=> __('Quantity')],
			['value'=>'width', 'label'=> __('Width')],
			['value'=>'height', 'label'=> __('Height')],
        ];
    }

}
