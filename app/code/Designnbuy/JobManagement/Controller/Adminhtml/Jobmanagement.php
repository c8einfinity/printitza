<?php
/**
 * Copyright © 2019 Design 'N' Buy. All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 * ALWAYS DO BETTER @a
 */

namespace Designnbuy\JobManagement\Controller\Adminhtml;

/**
 * Admin JobManagement controller
 */
class Jobmanagement extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'jobmanagement_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_JobManagement::jobmanagement';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\JobManagement\Model\Jobmanagement';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_JobManagement::jobmanagement';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
