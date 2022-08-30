<?php
/**
 * Copyright © 2015-2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Background\Block\Adminhtml\Grid\Column;

/**
 * Admin blog grid categories
 */
class Categories extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_rendererTypes['category'] = 'Designnbuy\Background\Block\Adminhtml\Grid\Column\Render\Category';
        $this->_filterTypes['category'] = 'Designnbuy\Background\Block\Adminhtml\Grid\Column\Filter\Category';
    }
}
