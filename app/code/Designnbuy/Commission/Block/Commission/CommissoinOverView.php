<?php

namespace Designnbuy\Commission\Block\Commission;

use Magento\Store\Model\ScopeInterface;

/**
 * Commissin OverView Block
 */

class CommissoinOverView extends \Magento\Framework\View\Element\Template
{
    const RESELLER = 1;

    const DESIGNER = 2;
    
    /**
     * @var \Designnbuy\Designer\Model\ResourceModel\Redemption\CollectionFactory
     */
    protected $_transactionCollection;

    /**
     * @var \Designnbuy\Design\Model\ResourceModel\Design\Collection
     */
    protected $_designCollection;

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
        array $data = []
    ) {
        parent::__construct($context, $data);
       // $this->designerFactory = $designerFactory;
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
        $this->dateFormate = $dateFormate;
        $this->customerFactory = $customerFactory;
    }

    protected function getCustomerSession()
    {
        return $this->_customerSession->create();
    }

    public function getDesignerCommissionCollection() 
    {
        $customerId = $this->getCustomerSession()->getId();

        $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
        $pageSize = ($this->getRequest()->getParam('limit'))? $this->getRequest()->getParam('limit') : 5;   

        $this->_transactionCollection = $this->commissionCollectionFactory->create();
        $this->_transactionCollection->getSelect()->where('main_table.user_id = '.$customerId);
        $this->_transactionCollection->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status', 'SO.created_at'));
        $this->_transactionCollection->setOrder('SO.created_at', 'DESC');
        $this->_transactionCollection->setPageSize($pageSize);
        $this->_transactionCollection->setCurPage($page);
    }

    /*public function getDesignerInformation() 
    {
        $customerId = $this->getCustomerSession()->getId();
        $_designer = $this->designerFactory->create();
        $_designer->load($customerId, 'customer_id');
        return $_designer;
        /*$designerData = array();
        $designerData['name'] = $this->getDesignerName($return = 'name', $_designer->getCustomerId());
        $designerData['email'] = $this->getDesignerName($return = 'email', $_designer->getCustomerId());
        $designerData['type'] = $this->getDesignerName($return = 'type', $_designer->getCustomerId());
        return $designerData;
    }*/

    public function getDesignerName($return, $id){
        $_customer = $this->customerFactory;
        $_customer->load($id);
        switch ($return) {
            case 'name':
                return $_customer->getFirstname() . ' ' . $_customer->getLastname();
                break;
            case 'email':
                return $_customer->getEmail();
                break;
            case 'type':
                if($_customer->getCustomertype() == self::DESIGNER){
                    return "Designer";
                }elseif($_customer->getCustomertype() == self::RESELLER){
                    return "Reseller";
                }
                break;
            default:
                break;
        }
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('MarketPlace Dashboard'));
        return $this;
    }

    public function getDesignerInfoLink() 
    {
        return $this->getUrl('designer/account/edit');
    }

    public function getCommissionReportLink()
    {
        return $this->getUrl('designer/commission/commissionreport');
    }

    public function setFormatedAmount($value) {
        return $this->pricingHelper->currency($value, true, false);
    }

    public function setDateFormate($date) {
        return $this->dateFormate->formatDate($date);
    }

    /**
     * Prepare Redemption Transaction collection
     *
     * @return \Designnbuy\Commission\Model\ResourceModel\Redemption\Collection
     */
    public function getCollection()
    {
        if (null === $this->_transactionCollection) {
            $this->getDesignerCommissionCollection();
        }
        return $this->_transactionCollection;
    }
}
