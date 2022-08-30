<?php

namespace Designnbuy\Commission\Controller\Adminhtml;

/**
 * Admin designer redemption edit controller
 */
class Redemption extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'designer_redemption_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Commission::redemption';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Commission\Model\Redemption';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Commission::redemption';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
