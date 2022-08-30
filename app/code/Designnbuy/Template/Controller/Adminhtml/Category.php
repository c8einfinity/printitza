<?php

namespace Designnbuy\Template\Controller\Adminhtml;

/**
 * Template Abstract Action
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
abstract class Category extends \Designnbuy\Template\Controller\Adminhtml\AbstractAction
{
    const PARAM_ID = 'id';

    /**
     * Check if admin has permissions to visit template pages.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
