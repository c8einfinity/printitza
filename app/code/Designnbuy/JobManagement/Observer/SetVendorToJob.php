<?php

namespace Designnbuy\JobManagement\Observer;
use Magento\Framework\Event\ObserverInterface;

class SetVendorToJob implements ObserverInterface
{
    /**
     * @var \Designnbuy\JobManagement\Model\JobmanagementFactory
     */
    protected $_jobManagementFactory;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Designnbuy\JobManagement\Model\JobmanagementFactory $jobManagementFactory,
        array $data = []
    ) {
        $this->_jobManagementFactory = $jobManagementFactory;
    }
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $itemId = $observer->getEvent()->getData('item_id');
        $vendorId = $observer->getEvent()->getData('vendor_user_id');
        $jobData = array();
        $jobManagement = $this->_jobManagementFactory->create()->load($itemId, 'item_id');
        $jobEntityId = $jobManagement->getEntityId();
        if(isset($jobEntityId) && $jobEntityId != ""){
            $jobData['vendor_user_id'] = $vendorId;
            $jobManagement->addData($jobData);
            $jobManagement->save();                    
        }
    }
}