<?php
namespace Designnbuy\Reseller\Controller\Adminhtml;

/**
 * Admin Reseller Product Pool edit controller
 */
class Productpool extends ProductpoolActions
{
    /**
     * Form session key
     * @var string
     */
    protected $_formSessionKey  = 'reseller_productpool_form_data';

    /**
     * Allowed Key
     * @var string
     */
    protected $_allowedKey      = 'Designnbuy_Reseller::productpool';

    /**
     * Model class name
     * @var string
     */
    protected $_modelClass      = 'Designnbuy\Reseller\Model\Productpool';

    /**
     * Active menu key
     * @var string
     */
    protected $_activeMenu      = 'Designnbuy_Base::network';

    /**
     * Status field name
     * @var string
     */
    protected $_statusField     = 'is_active';
}
