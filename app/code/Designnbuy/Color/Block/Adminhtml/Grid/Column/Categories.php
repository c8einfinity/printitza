<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Adminhtml\Grid\Column;

/**
 * Admin color grid Categories
 */
class Categories extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_rendererTypes['category'] = 'Designnbuy\Color\Block\Adminhtml\Grid\Column\Render\Category';
        $this->_filterTypes['category'] = 'Designnbuy\Color\Block\Adminhtml\Grid\Column\Filter\Category';
    }
}
