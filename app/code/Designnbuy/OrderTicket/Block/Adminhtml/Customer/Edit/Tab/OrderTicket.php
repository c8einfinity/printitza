<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\Customer\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;

/**
 * Order ORDERTICKET Grid
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class OrderTicket extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid implements TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory
     * @param \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\CollectionFactory $collectionFactory,
        \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->customerRepository = $customerRepository;
        parent::__construct($context, $backendHelper, $collectionFactory, $orderticketFactory, $moduleManager, $data);
    }

    /**
     * Initialize customer edit tab orderticket
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('customer_edit_tab_orderticket');
        $this->setUseAjax(true);
    }

    /**
     * Prepare massaction
     *
     * @return $this
     */
    protected function _prepareMassaction()
    {
        return $this;
    }

    /**
     * Configuring and setting collection
     *
     * @return $this
     */
    protected function _beforePrepareCollection()
    {
        $customerId = null;
        $customer = $this->customerRepository->getById(
            $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)
        );
        if ($customer && $customer->getId()) {
            $customerId = $customer->getId();
        } elseif ($this->getCustomerId()) {
            $customerId = $this->getCustomerId();
        }
        if ($customerId) {
            /** @var $collection \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\Grid\Collection */
            $collection = $this->_collectionFactory->create()->addFieldToFilter('customer_id', $customerId);

            $this->setCollection($collection);
        }
        return $this;
    }

    /**
     * Prepare grid columns
     *
     * @return \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Grid
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();
    }

    /**
     * Get Url to action
     *
     * @param  string $action action Url part
     * @return string
     */
    protected function _getControllerUrl($action = '')
    {
        return '*/orderticket/' . $action;
    }

    /**
     * Get Url to action to reload grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/orderticket/orderticketCustomer', ['_current' => true]);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * ######################## TAB settings #################################
     */
    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Order Tickets');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Order Tickets');
    }

    /**
     * Check if can show tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        $customerId = $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
        return (bool)$customerId;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
        return '';
    }

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return false;
    }
}
