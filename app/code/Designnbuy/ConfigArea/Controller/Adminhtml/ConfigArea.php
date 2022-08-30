<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Controller\Adminhtml;

/**
 * Admin configarea configarea edit controller
 */
class ConfigArea extends Actions
{
	/**
	 * Form session key
	 * @var string
	 */
    protected $_formSessionKey  = 'configarea_configarea_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_ConfigArea::configarea';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\ConfigArea\Model\ConfigArea';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_ConfigArea::configarea';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';

}
