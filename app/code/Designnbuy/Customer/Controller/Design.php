<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer subscribe controller
 */
namespace Designnbuy\Customer\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\DesignFactory;
use Magento\Customer\Model\Url as CustomerUrl;

abstract class Design extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var Session
     */
    protected $_customerSession;

    /**
     * Design factory
     *
     * @var DesignFactory
     */
    protected $_designFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerUrl
     */
    protected $_customerUrl;

    /**
     * @param Context $context
     * @param DesignFactory $designFactory
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     */
    public function __construct(
        Context $context,
        DesignFactory $designFactory,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_designFactory = $designFactory;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
    }
}
