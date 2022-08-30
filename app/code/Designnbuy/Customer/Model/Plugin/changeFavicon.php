<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\Plugin;

class changeFavicon
{
    public function afterGetDefaultFavicon(\Magento\Theme\Model\Favicon\Favicon $subject)
    {
        return "Designnbuy_Customer::images/dnb_favicon.jpg";
    }
}
