<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Controller\Adminhtml;

/**
 * Admin color color edit controller
 */
class Color extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'color_color_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Color::color';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Color\Model\Color';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Color::color';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
