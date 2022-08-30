<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\CommissionReport\Block\Adminhtml;

/**
 * Adminhtml sales report page content block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 100.0.2
 */
class DesignerDashboard extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\CommissionReport\Model\ResourceModel\Design\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_pricingHelper = $pricingHelper;
    }

    public function getDesignCommissionDashboard()
    {
        $commissionCollection = $this->_collectionFactory->create();
        $reportData = array();
        $reportData['total_commission_paid'] = 0;
        $reportData['total_design_sold'] = 0;
        $reportData['total_designer'] = 0;
        $reportData['total_reseller'] = 0;

        foreach($commissionCollection as $item){
            $reportData['total_commission_paid'] += $item->getCommissionAmount();
            $reportData['total_design_sold'] += $item->getItemQty();

            if($item->getUserType() == 2) {
                $designerCount = $this->getTotalCustomerTypeCountCollection($item->getUserType());
                $reportData['total_designer'] = $designerCount;
            }elseif($item->getUserType() == 1){
                $resellerCount = $this->getTotalCustomerTypeCountCollection($item->getUserType());
                $reportData['total_reseller'] = $resellerCount;                
            }
        }
        return $reportData;
    }

    public function getTotalCustomerTypeCountCollection($userType) 
    {

        $customerCollection = $this->_customerFactory->create();
        $customerCollectionData = $customerCollection->addFieldToFilter('customertype', $userType)->getColumnValues('customertype');
        return count($customerCollectionData);
    }

    public function setFormatedAmount($value) 
    {
        return $this->_pricingHelper->currency($value, true, false);
    }
}
