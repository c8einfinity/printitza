<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Template Filter Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Model\Template;

class Filter extends \Magento\Widget\Model\Template\FilterEmulate
{
    /**
     * Generate widget HTML if template variables are assigned
     *
     * @param string[] $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        if (!isset($this->templateVars['design'])) {
            return $construction[0];
        }
        $construction[2] .= sprintf(' store_id ="%s"', $this->getStoreId());
        return parent::widgetDirective($construction);
    }
}
