<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * Manage Customer Template Controller
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Controller\Adminhtml;

abstract class Template extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Designnbuy_Customer::template';
}
