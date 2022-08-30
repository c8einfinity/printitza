<?php
namespace Unific\Connector\Plugin;

use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;

class CheckoutPlugin extends CheckoutEnrichPlugin
{
    /**
     * @param ShippingInformationManagementInterface $subject
     * @param PaymentDetailsInterface $result
     * @param int $cartId
     * @return PaymentDetailsInterface
     */
    public function afterSaveAddressInformation(
        ShippingInformationManagementInterface $subject,
        PaymentDetailsInterface $result,
        int $cartId
    ) {
        $this->callWebhooks($cartId);

        return $result;
    }
}
