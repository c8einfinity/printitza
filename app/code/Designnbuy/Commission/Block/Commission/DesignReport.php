<?php

namespace Designnbuy\Commission\Block\Commission;

use Magento\Store\Model\ScopeInterface;

/**
 * Abstract design design list block
 */

class DesignReport extends \Magento\Framework\View\Element\Template
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
       // \Designnbuy\Designer\Model\DesignerFactory $designerFactory,
        \Magento\Customer\Model\SessionFactory $customerSession,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateFormate,
        \Magento\Customer\Model\Customer $customerFactory,
        \Designnbuy\Designidea\Model\DesignideaFactory $designIdeaFactory,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        //$this->designerFactory = $designerFactory;
        $this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->pricingHelper = $pricingHelper;
        $this->dateFormate = $dateFormate;
        $this->customerFactory = $customerFactory;
        $this->designIdeaFactory = $designIdeaFactory;
        $this->templateFactory = $templateFactory;
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

        $this->_transactionCollection = $this->commissionCollectionFactory->create();
        $this->_transactionCollection->getSelect()->where('main_table.user_id = '.$_designer->getCustomerId());
        $this->_transactionCollection->getSelect()->joinLeft(array("SO" => 'sales_order'), "SO.increment_id = main_table.order_id", array('SO.status', 'SO.created_at'));

        if($this->getRequest()->getParam('name')):
            $this->_transactionCollection->addFieldToFilter('item_name', array('like' => '%'.$this->getRequest()->getParam('name').'%'));
        endif;

        if($this->getRequest()->getParam('design_id')):
            $this->_transactionCollection->addFieldToFilter('item_id', $this->getRequest()->getParam('design_id'));
        endif;

        $shortBy = ($this->getRequest()->getParam('report_list_order'))? $this->getRequest()->getParam('report_list_order') : 'item_purchased_id';
        $dir = ($this->getRequest()->getParam('design_list_dir'))? $this->getRequest()->getParam('design_list_dir') : 'asc';

        if(isset($shortBy) && $shortBy != '') {
            $this->_transactionCollection->setOrder($shortBy, $dir);
        }else{
            $this->_transactionCollection->setOrder('created_at', 'DESC');
        }

        $this->_transactionCollection->setPageSize($pageSize);
        $this->_transactionCollection->setCurPage($page);
    }*/

    public function designReportShortingOption(){
        return array(
            [
                'value' => 'item_purchased_id', 
                'label' => 'Design Id'
            ],
            [
                'value' => 'commission_amount',
                'label' => 'Commission'
            ],
            [
                'value' => 'item_qty',
                'label' => 'Quantity'
            ]
        );
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Designer Design Report'));
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

    public function getDesignLink($entityId){
        /*$desing = $this->designIdeaFactory->create();
        $desing->load($entityId);
        return $this->getUrlWithIdentifier($desing->getIdentifier());*/
        return $this->getUrl('designer/commission/designdetailreport', [ 'id' => $entityId ]);
    }

    public function getUrlWithIdentifier($identifier) {
        return $this->getUrl(self::DESIGN_CONTROLLER . '/' . $identifier);
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
