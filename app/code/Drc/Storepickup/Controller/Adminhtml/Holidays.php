<?php
/**
 * Copyright © 2019 Designnbuy WebToPrint Pvt. Ltd. All rights reserved.
 * See LICENSE.txt for license details.
 * ALWAYS DO BETTER @a
 */

namespace Drc\Storepickup\Controller\Adminhtml;

abstract class Holidays extends \Magento\Backend\App\Action
{
    /**
     * Holidays Factory
     *
     * @var \Drc\Storepickup\Model\HolidaysFactory
     */
    protected $holidaysFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     *
     * @param \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Drc\Storepickup\Model\HolidaysFactory $holidaysFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {        
        $this->holidaysFactory   = $holidaysFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Holidays
     *
     * @return \Drc\Storepickup\Model\Holidays
     */
    protected function initHolidays()
    {
        $holidaysId  = (int) $this->getRequest()->getParam('entity_id');
        /** @var \Drc\Storepickup\Model\Holidays $holidays */        
        $holidays    = $this->holidaysFactory->create();
        if ($holidaysId) {
            $holidays->load($holidaysId);
        }
        $this->coreRegistry->register('drc_storepickup_holidays', $holidays);
        return $holidays;
    }
}
