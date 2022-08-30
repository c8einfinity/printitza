<?php


namespace Designnbuy\Notifications\Controller\Adminhtml;

/**
 * Admin Notifications edit controller
 */
class Notifications extends Actions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'notifications_notifications_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Notifications::notifications';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Notifications\Model\Notifications';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Notifications::notifications';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_read';
}
