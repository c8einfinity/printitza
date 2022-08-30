<?php
namespace Drc\Storepickup\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    protected $quoteRepository;

    public function __construct(
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        //shipping_method_code
        $deliveryDate = $extAttributes->getDeliveryDate();
        $quote = $this->quoteRepository->getActive($cartId);
        $quote->setDeliveryDate($deliveryDate);
        $storeList = '';
        if($addressInformation->getShippingMethodCode() == 'storepickup'){
            $storeList = $extAttributes->getStoreList();
        }
        $quote->setStoreList($storeList);
    }
}
