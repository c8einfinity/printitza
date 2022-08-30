<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */


namespace Designnbuy\Vendor\Plugin\Ui;

class MassAction
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @var \Designnbuy\Vendor\Helper\Data
     */
    protected $vendorData;


    public function __construct(
        \Magento\Framework\AuthorizationInterface $authorization,
        \Designnbuy\Vendor\Helper\Data $vendorData
    )
    {
        $this->_authorization = $authorization;
        $this->vendorData = $vendorData;
    }

    public function afterGetChildComponents(
        \Magento\Ui\Component\MassAction $subject,
        $result
    ) {
        if($this->vendorData->getVendorUser()){
            switch ($subject->getContext()->getNamespace()) {
                case 'sales_order_grid':
                    $result = [];
                    //unset($result['subscribe'], $result['unsubscribe'], $result['assign_to_group'], $result['edit']);
                    break;
            }
        }

        return $result;
    }
}
