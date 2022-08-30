<?php
namespace Designnbuy\Reseller\Model\Observer;

use Designnbuy\Reseller\Model\Admin;
/**
 * Abstract adminGws observer
 *
 */
class AbstractClass
{
    /**
     * @var Role
     */
    protected $_reseller;

    /**
     * Initialize helper
     *
     * @param Role $role
     */
    public function __construct(Admin $reseller)
    {
        $this->_reseller = $reseller;
    }
}
