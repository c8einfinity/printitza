<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Size;

/**
 * Class DuplicateButton
 */
class DuplicateButton extends \Designnbuy\Sheet\Block\Adminhtml\Edit\DuplicateButton
{
    /**
     * @return array|string
     */
    public function getButtonData()
    {
        if (!$this->authorization->isAllowed("Designnbuy_Sheet::size_save")) {
            return [];
        }
        return parent::getButtonData();
    }
}
