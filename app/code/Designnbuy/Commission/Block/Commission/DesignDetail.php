<?php

namespace Designnbuy\Commission\Block\Commission;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract design design list block
 */

class DesignDetail extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory
     */
    protected $_transactionCollection;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormate,
        \Designnbuy\Commission\Model\CommissionFactory $designideaFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
        $this->dateFormate = $dateFormate;
        $this->designideaFactory = $designideaFactory;
    }

    protected function getCustomerSession()
    {
        return $this->_customerSession->create();
    }

    public function getDesignEarningCollection() 
    {
        $customerId = $this->getCustomerSession()->getId();
        $designId = $this->getRequest()->getParam('id');

        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;   

        $this->_transactionCollection = $this->commissionCollectionFactory->create();
        $this->_transactionCollection->addFieldToFilter('item_id', $designId);

        $this->_transactionCollection->getSelect()->where('main_table.user_id = '.$customerId);
        $this->_transactionCollection->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status', 'SO.created_at'));

        $this->_transactionCollection->setPageSize($pageSize);
        $this->_transactionCollection->setCurPage($page);
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Design Earning'));
        if ($this->getCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'designer.transaction.pager'
            )->setAvailableLimit(array(5=>5,10=>10,15=>15))->setShowPerPage(true)->setCollection(
                $this->getCollection()
            );
            $this->setChild('pager', $pager);
            $this->getCollection()->load();
        }
        return $this;
    }

    public function setFormatedAmount($value) {
        return $this->pricingHelper->currency($value, true, false);
    }

    public function setDateFormate($date) {
        return $this->dateFormate->formatDate($date);
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getDesignName() 
    {
        $designId = $this->getRequest()->getParam('id');
        $_design = $this->designideaFactory->create();
        $_design->load($designId, 'item_id');
        return $_design->getItemName();
    }

    /**
     * Prepare Commission Transaction collection
     *
     * @return \Designnbuy\Commission\Model\ResourceModel\Commission\Collection
     */
    public function getCollection()
    {
        if (null === $this->_transactionCollection) {
            $this->getDesignEarningCollection();
        }
        return $this->_transactionCollection;
    }
}
