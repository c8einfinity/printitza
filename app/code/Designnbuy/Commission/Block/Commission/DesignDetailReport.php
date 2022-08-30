<?php

namespace Designnbuy\Commission\Block\Commission;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract design design list block
 */

class DesignDetailReport extends \Magento\Framework\View\Element\Template
{
    const DESIGN_CONTROLLER = 'design';
    /**
     * @var \Designnbuy\Designer\Model\ResourceModel\Redemption\CollectionFactory
     */
    protected $_transactionCollection;

    /**
     * @var \Designnbuy\Design\Model\ResourceModel\Design\Collection
     */
    protected $_designCollection;

    /**
     * @var \Designnbuy\Designer\Model\Url
     */
    protected $_url;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        //\Designnbuy\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormate,
        \Magento\Customer\Model\Customer $customerFactory,
        \Designnbuy\Commission\Model\CommissionFactory $commissionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        //$this->designerFactory = $designerFactory;
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
        $this->dateFormate = $dateFormate;
        $this->customerFactory = $customerFactory;
        $this->_commissionFactory = $commissionFactory;
    }

    protected function getCustomerSession()
    {
        return $this->_customerSession->create();
    }

    /*public function getDesignerDesignReportCollection() 
    {
        $customerId = $this->getCustomerSession()->getId();
        $_designer = $this->designerFactory->create();
        $_designer->load($customerId, 'customer_id');
        
        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;
        $designId = $this->getRequest()->getParam('id');
        $this->_transactionCollection = $this->commissionCollectionFactory->create();
        if(isset($designId) && $designId != ''){
            $this->_transactionCollection->addFieldToFilter('item_id',$designId);
        }
        $this->_transactionCollection->getSelect()->where('main_table.user_id = '.$_designer->getCustomerId());
        $this->_transactionCollection->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status', 'SO.created_at'));
        
        $this->_transactionCollection->setOrder('created_at', 'DESC');
        $this->_transactionCollection->setPageSize($pageSize);
        $this->_transactionCollection->setCurPage($page);
    }*/

    protected function _prepareLayout()
    {
        $id = $this->getRequest()->getParam('id');
        //$_commission = $this->_commissionFactory->create()->load($id, 'item_id');
        $this->pageConfig->getTitle()->set(__('Detail Design Report'));
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

    /**
     * Prepare Redemption Transaction collection
     *
     * @return \Designnbuy\Commission\Model\ResourceModel\Redemption\Collection
     */
    public function getCollection()
    {
        /*if (null === $this->_transactionCollection) {
            $this->getDesignerDesignReportCollection();
        }*/
        return $this->_transactionCollection;
    }
}
