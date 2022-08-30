<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Controller\Adminhtml;

/**
 * Admin background background edit controller
 */
class Background extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'background_background_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Background::background';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Background\Model\Background';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Background::background';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
