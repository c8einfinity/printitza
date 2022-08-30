<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Reseller\Model\Customer;

class DataProviderWithDefaultAddresses
{
    /**
     * @var \Designnbuy\Reseller\Model\Admin
     */
    protected $_reseller;

    public function __construct(
        \Designnbuy\Reseller\Model\Admin $reseller
    ) {
        $this->_reseller = $reseller;
    }

    public function afterGetMeta(\Magento\Customer\Model\Customer\DataProviderWithDefaultAddresses $subject, $result)
	{
        if ($this->_reseller->isResellerAdmin()) {
            unset($result['customer']['children']['customer_user_type']);
        }
        return $result;
	}
}
