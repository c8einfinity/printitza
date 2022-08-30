<?php

namespace Unific\Connector\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Model\Quote\Address;

class GuestCheckoutPlugin extends CheckoutEnrichPlugin
{
    /**
     * @param ShipmentEstimationInterface $subject
     * @param ShippingMethodInterface[] $returnValue
     * @param $cartId
     * @param Address $address
     * @return array
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function afterEstimateByExtendedAddress(
        ShipmentEstimationInterface $subject,
        $returnValue,
        $cartId,
        Address $address
    ) {
        $this->callWebhooks($cartId, $address);

        return $returnValue;
    }
}
