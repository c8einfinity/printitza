<?php

namespace Designnbuy\Commission\Block\Adminhtml;

/**
 * Admin designer commission
 */
class UserRedemption extends \Magento\Framework\View\Element\Template
{
	const ACTIVE = 1;
    
    const INACTIVE = 0;
    
    const STATUS_INACTIVE = 0;

    const RESELLER = 1; // USER TYPE

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Commission\Model\ResourceModel\Commission\CollectionFactory $commissionCollectionFactory,
        \Designnbuy\Commission\Model\ResourceModel\Redemption\CollectionFactory $transactionCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Designnbuy\Commission\Helper\Data $commissionHelper,
        array $data = []
    ) {
    	$this->commissionCollectionFactory = $commissionCollectionFactory;
        $this->transactionCollectionFactory = $transactionCollectionFactory;
        $this->pricingHelper = $pricingHelper;
        $this->commissionHelper = $commissionHelper;
        parent::__construct($context, $data);
    }

    public function getStoreOwnerCommission() {
        
        $commissionInfo = array();

        $resellerUserId = $this->commissionHelper->getOwnerUserId();

        $commissionCollection = $this->commissionCollectionFactory->create()
            ->addFieldToFilter('user_id', $resellerUserId)
            ->getColumnValues('commission_amount');

        $commissionInfo['canceledCommission'] = 0;
        $commissionInfo['pendingCommission'] = 0;
        $commissionInfo['earnedCommission'] = array_sum($commissionCollection);

        //for pending commission
        $commissionCollection = $this->commissionCollectionFactory->create();
        $commissionCollection->getSelect()->where('main_table.user_id = '.$resellerUserId);
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
            ->addFieldToFilter('user_id', $resellerUserId)
            ->addFieldToFilter('is_active', self::ACTIVE)
            ->getColumnValues('commission_amount');

        $commissionInfo['redeemedCommission'] = array_sum($transactionCollection);

        $pendingTransactionCollection = $this->transactionCollectionFactory->create();
        $commissionPendingApproval = $pendingTransactionCollection->addFieldToFilter('user_id', $resellerUserId)->addFieldToFilter('is_active', self::INACTIVE)->getColumnValues('commission_amount');
        $commissionInfo['pendingApproveCommission'] = array_sum($commissionPendingApproval);

        $commissionInfo['balance'] = ($commissionInfo['earnedCommission'] - $commissionInfo['pendingCommission']) - $commissionInfo['redeemedCommission'] - $commissionInfo['pendingApproveCommission'];

        return $commissionInfo;
    }

    public function setFormatedAmount($value) {
        return $this->pricingHelper->currency($value, true, false);
    }

    public function getRedemptionPostUrl()
    {
    	return $this->getUrl('commission/redemption/ownerredemptionpost');
    }
}
