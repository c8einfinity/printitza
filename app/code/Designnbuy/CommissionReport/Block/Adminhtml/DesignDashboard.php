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
class DesignDashboard extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\CommissionReport\Model\ResourceModel\Design\CollectionFactory $collectionFactory,        
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->_pricingHelper = $pricingHelper;
    }

    public function getDesignCommissionDashboard()
    {
        $commissionCollection = $this->_collectionFactory->create();
        $reportData = array();
        $reportData['total_commission_paid'] = 0;
        $reportData['total_design_sold'] = 0;
        $reportData['data_for'] = 'All';

        foreach($commissionCollection as $item){
            $reportData['total_commission_paid'] += $item->getCommissionAmount();
            $reportData['total_design_sold'] += $item->getItemQty();
        }
        return $reportData;
    }

    public function setFormatedAmount($value) 
    {
        return $this->_pricingHelper->currency($value, true, false);
    }
}
