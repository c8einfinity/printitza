<?php

namespace Designnbuy\Vendor\Plugin\Sales\Order\Item;

class FilterOrderItemByVendorAdmin {

    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;

    public function __construct(
        \Designnbuy\Vendor\Helper\Data $vendorData
    ){
        $this->vendorData = $vendorData;
    }
    /**
     * Sets a filter on the category collection
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $order
     * @param \Magento\Framework\Data\Collection $orderItemCollection
     */
    public function beforeLoad(
        \Magento\Sales\Model\ResourceModel\Order\Item\Collection $orderItemCollection, $printQuery = false, $logQuery = false
    ) {
        if($this->vendorData->getVendorUser()) {
            $vendorUser = $this->vendorData->getVendorUser();
            if ($vendorUser->getUserId()) {
                $orderItemCollection->addFieldToFilter('vendor_id', $vendorUser->getUserId());
            }
        }
    }

}