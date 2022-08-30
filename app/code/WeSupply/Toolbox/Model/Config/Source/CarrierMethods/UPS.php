<?php

namespace WeSupply\Toolbox\Model\Config\Source\CarrierMethods;

use \Magento\Shipping\Model\CarrierFactoryInterface;

class UPS implements \Magento\Framework\Option\ArrayInterface
{
    private $carrierFactory;

    const CARRIER_CODE = 'ups';

    public function __construct(CarrierFactoryInterface $carrierFactory)
    {
        $this->carrierFactory = $carrierFactory;
    }

    public function toOptionArray()
    {
        $carrier = $this->carrierFactory->create(self::CARRIER_CODE);
        $allowedMethods = $carrier->getAllowedMethods();
        $options = array();

        foreach($allowedMethods as $code => $method)
        {
            $options[] = ['value' => $code, 'label' =>$method ];
        }

        return $options;
    }
}