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
class OrderDashboard extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\CommissionReport\Model\ResourceModel\Order\CollectionFactory $collectionFactory,        
        \Designnbuy\Commission\Model\ResourceModel\Redemption\CollectionFactory $transactionCollection,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_collectionFactory = $collectionFactory;
        $this->_transactionCollection = $transactionCollection;
        $this->_pricingHelper = $pricingHelper;
        $this->_date = $date;
    }

    public function getOrderCommissionDashboard()
    {
        $commissionCollection = $this->_collectionFactory->create();
        
        $transactionCollection = $this->_transactionCollection->create();

        $reportData = array();

        $reportData['total_commission'] = 0;
        $reportData['total_commission_paid'] = 0;
        $reportData['total_commission_pending'] = 0;
        $reportData['canceled_commission'] = 0;
        $reportData['data_for'] = 'All';

        foreach($commissionCollection as $data){
            if($data->getStatus() == 'canceled'){
                $reportData['canceled_commission'] += $data->getCommissionAmount();
            }
            else{
                $reportData['total_commission'] += $data->getCommissionAmount();
            }
        }

        foreach($transactionCollection as $transaction){
            if($transaction->getIsActive() == 1) {
                $reportData['total_commission_paid'] += $transaction->getCommissionAmount();
            }
        } 
        $reportData['last_payment_date'] = $transactionCollection->getLastItem()->getUpdateTime();

        if($transactionCollection->getSize() == 0){
            $reportData['last_payment_date'] = 'No Transactions';
        }
        $reportData['total_commission_pending'] = $reportData['total_commission'] - $reportData['total_commission_paid'];
        
        return $reportData;        
    }

    public function setFormatedDate($date) 
    {
        return $this->_date->date(new \DateTime($date))->format('M d, Y H:i:s');
    }

    public function setFormatedAmount($value) 
    {
        return $this->_pricingHelper->currency($value, true, false);        
    }
}
