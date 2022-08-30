<?php

namespace Designnbuy\Commission\Block\Commission;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract design design list block
 */

abstract class AbstractRedemption extends \Magento\Framework\View\Element\Template
{
    const ACTIVE = 1;
    
    const INACTIVE = 0;
    
    const STATUS_INACTIVE = 0;

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

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Designnbuy\Designer\Model\ResourceModel\Design\CollectionFactory $designCollectionFactory
     * @param \Designnbuy\Designer\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        //\Designnbuy\Designer\Model\DesignerFactory $designerFactory,
        \Designnbuy\Commission\Model\ResourceModel\Redemption\CollectionFactory $transactionCollectionFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormate,
        \Magento\Customer\Model\Customer $customerFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
       // $this->designerFactory = $designerFactory;
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
        $this->dateFormate = $dateFormate;
        $this->customerFactory = $customerFactory;
    }

    protected function getCustomerSession()
    {
        return $this->_customerSession->create();
    }

    /*public function getDesignerCommissionCollection() {
        $commissionInfo = array();
        $customerId = $this->getCustomerSession()->getId();

        $_designer = $this->designerFactory->create();
        $_designer->load($customerId, 'customer_id');
        
        /*Earned Commissoin collection from designnbuy_designer_commission_transaction table
        $commissionFactory = $this->commissionCollectionFactory->create();
        $transactionCollection = $this->transactionCollectionFactory->create();
        $pendingTransactionCollection = $this->transactionCollectionFactory->create();
        
        $transactionCollection = $transactionCollection->addFieldToFilter('user_id', $_designer->getCustomerId())->addFieldToFilter('is_active', self::ACTIVE)->getColumnValues('commission_amount');

        $commissionInfo['earnedCommission'] = array_sum($transactionCollection);
        //$commissionInfo['earnedCommission'] = 0;
        $commissionPendingApproval = $pendingTransactionCollection->addFieldToFilter('user_id', $_designer->getCustomerId())->addFieldToFilter('is_active', self::INACTIVE)->getColumnValues('commission_amount');

        $commissionInfo['pendingApproveCommission'] = array_sum($commissionPendingApproval);

        $commissionInfo['canceledCommission'] = 0;
        /*Pending Commissoin Calculation from designnbuy_designer_commission table
        $commissionFactory->getSelect()->where('main_table.user_id = '.$_designer->getCustomerId());
        $commissionFactory->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status'));
        
        $commissionInfo['pendingCommission'] = 0;

        $commissionInfo['totalcommissin'] = 0;

        foreach($commissionFactory as $commission){
            $commissionInfo['earnedCommission'] += $commission->getCommissionAmount();
            if($commission->getStatus() != 'complete' && $commission->getStatus() != 'canceled'){
                $commissionInfo['pendingCommission'] += $commission->getCommissionAmount();
            }
            
            if($commission->getStatus() == 'canceled'){
                $commissionInfo['canceledCommission'] += $commission->getCommissionAmount();              
            }

            if($commission->getStatus() == 'complete'){
                $commissionInfo['totalcommissin'] += $commission->getCommissionAmount();
            }
        }

        $commissionInfo['earnedCommission'] -= $commissionInfo['canceledCommission'];
        $commissionInfo['redeemedCommission'] = array_sum($transactionCollection);
        $commissionCollection = $this->commissionCollectionFactory->create();
        $commissionCollection->addFieldToFilter('user_id', $customerId);

        $commissionInfo['balance'] = ($commissionInfo['earnedCommission'] - $commissionInfo['pendingCommission']) - $commissionInfo['redeemedCommission'];

        $commissionInfo['totalPendingCommission'] = $commissionInfo['pendingCommission'] + $commissionInfo['pendingApproveCommission'];

        $commissionInfo['balance'] = ($commissionInfo['totalcommissin'] - $commissionInfo['pendingCommission']) - $commissionInfo['redeemedCommission'] - $commissionInfo['pendingApproveCommission'];

        return $commissionInfo;
    }*/

    public function getDesignerCommission(){
        $commissionInfo = array();

        $customerId = $customerId = $this->getCustomerSession()->getId();

        $commissionCollection = $this->commissionCollectionFactory->create()
            ->addFieldToFilter('user_id', $customerId)
            ->getColumnValues('commission_amount');

        $commissionInfo['canceledCommission'] = 0;
        $commissionInfo['pendingCommission'] = 0;
        $commissionInfo['earnedCommission'] = array_sum($commissionCollection);

        //for pending commission
        $commissionCollection = $this->commissionCollectionFactory->create();
        $commissionCollection->getSelect()->where('main_table.user_id = '.$customerId);
        $commissionCollection->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status'));

        foreach($commissionCollection as $commission){
            if($commission->getStatus() != 'complete' && $commission->getStatus() != 'canceled'){
                $commissionInfo['pendingCommission'] += $commission->getCommissionAmount();
            }

            if($commission->getStatus() == 'canceled'){
                $commissionInfo['canceledCommission'] += $commission->getCommissionAmount();
            }
        }

        $commissionInfo['earnedCommission'] -= $commissionInfo['canceledCommission'];

        $transactionCollection = $this->transactionCollectionFactory->create()
            ->addFieldToFilter('user_id', $customerId)
            ->addFieldToFilter('is_active', self::ACTIVE)
            ->getColumnValues('commission_amount');

        $commissionInfo['redeemedCommission'] = array_sum($transactionCollection);

        $pendingTransactionCollection = $this->transactionCollectionFactory->create();
        $commissionPendingApproval = $pendingTransactionCollection->addFieldToFilter('user_id', $customerId)->addFieldToFilter('is_active', self::INACTIVE)->getColumnValues('commission_amount');
        $commissionInfo['pendingApproveCommission'] = array_sum($commissionPendingApproval);

        $commissionInfo['balance'] = ($commissionInfo['earnedCommission'] - $commissionInfo['pendingCommission']) - $commissionInfo['redeemedCommission'] - $commissionInfo['pendingApproveCommission'];

        return $commissionInfo;

    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Designer Commission Redemption'));
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

    public function getDesignerCommissionTransactionCollection() {
        
        $customerId = $this->getCustomerSession()->getId();

        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;

        $this->_transactionCollection = $this->transactionCollectionFactory->create();
        $this->_transactionCollection->addFieldToFilter('user_id', $customerId);
        $this->_transactionCollection->setOrder('creation_time', 'DESC');
        //$this->_transactionCollection->setPageSize($pageSize);
        //$this->_transactionCollection->setCurPage($page);
        //return $this->_transactionCollection;
    }
    public function getDesignerPenddingCommission() {
        
        $customerId = $this->getCustomerSession()->getId();

        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 1;

        $this->_transactionCollection = $this->transactionCollectionFactory->create();
        $this->_transactionCollection->addFieldToSelect('commission_amount');
        $this->_transactionCollection->addFieldToFilter('user_id', $customerId);
        $this->_transactionCollection->addFieldToFilter('is_active', '0');
        $this->_transactionCollection->getSelect()->columns(['commission_amount' => new \Zend_Db_Expr('SUM(commission_amount)')])->group('user_id');
        if (!empty($this->_transactionCollection->getData())) {
            return $this->_transactionCollection->getData('commission_amount')[0]['commission_amount'];
        }
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
        if (null === $this->_transactionCollection) {
            $this->getDesignerCommissionTransactionCollection();
        }
        return $this->_transactionCollection;
    }

    public function getCustomerInformation() 
    {
        $customerId = $this->getCustomerSession()->getId();
        $_customer = $this->customerFactory->load($customerId);
        return $_customer;
    }

    public function getUserFullName(){
        $_customer = $this->getCustomerInformation();
        $fullName = $_customer->getFirstname(). ' ' .$_customer->getLastname();
        return $fullName;
    }

    public function getUserId() {
        return $this->getCustomerSession()->getId();
    }

    public function getDefaultStatus(){
        return self::STATUS_INACTIVE;
    }
}
