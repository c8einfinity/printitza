<?php

namespace Designnbuy\Designidea\Controller\Adminhtml;

/**
 * Designidea Abstract Action
 * @category Designnbuy
 * @package  Designnbuy_Designidea
 * @module   Designidea
 * @author   Designnbuy Developer
 */
abstract class Category extends \Designnbuy\Designidea\Controller\Adminhtml\AbstractAction
{
    const PARAM_ID = 'id';

    /**
     * Check if admin has permissions to visit designidea pages.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Designnbuy_Designidea::category');
    }
}
