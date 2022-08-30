<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Controller\Adminhtml;

/**
 * Admin clipart clipart edit controller
 */
class Clipart extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'clipart_clipart_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Clipart::clipart';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Clipart\Model\Clipart';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Clipart::clipart';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
