<?php

namespace Unific\Connector\Plugin;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\Data\ShippingMethodInterface;
use Magento\Quote\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Quote\Address;

class CustomerCheckoutPlugin extends CheckoutEnrichPlugin
{
    /**
     * @param ShippingMethodManagementInterface $subject
     * @param ShippingMethodInterface[] $returnValue
     * @param $cartId
     * @param int $addressId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterEstimateByAddressId(
        ShippingMethodManagementInterface $subject,
        $returnValue,
        $cartId,
        int $addressId
    ) {
        $this->callWebhooks($cartId);

        return $returnValue;
    }

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
