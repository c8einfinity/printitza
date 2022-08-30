<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Grid\Column;

/**
 * Admin sheet grid size
 */
class Size extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->_rendererTypes['size'] = 'Designnbuy\Sheet\Block\Adminhtml\Grid\Column\Render\Size';
        $this->_filterTypes['size'] = 'Designnbuy\Sheet\Block\Adminhtml\Grid\Column\Filter\Size';
    }
}
