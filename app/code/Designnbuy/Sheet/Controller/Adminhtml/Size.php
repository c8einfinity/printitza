<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Controller\Adminhtml;

/**
 * Admin sheet size edit controller
 */
class Size extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'sheet_size_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Sheet::size';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Sheet\Model\Size';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Sheet::size';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
