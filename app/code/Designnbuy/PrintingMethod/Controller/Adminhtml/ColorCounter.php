<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Controller\Adminhtml;

/**
 * Admin printingmethod colorcounter edit controller
 */
class ColorCounter extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'printingmethod_colorcounter_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_PrintingMethod::printingmethod';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\PrintingMethod\Model\ColorCounter';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_PrintingMethod::printingmethod';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
