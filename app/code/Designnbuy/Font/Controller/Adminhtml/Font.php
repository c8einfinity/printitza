<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Controller\Adminhtml;

/**
 * Admin font font edit controller
 */
class Font extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'font_font_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Font::font';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Font\Model\Font';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Font::font';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
