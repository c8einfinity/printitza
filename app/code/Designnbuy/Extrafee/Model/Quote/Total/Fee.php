<?php
namespace Designnbuy\Extrafee\Model\Quote\Total;

use Magento\Store\Model\ScopeInterface;

class Fee extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{

    protected $helperData;
	protected $_priceCurrency;

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    /**
     * @var \Designnbuy\PrintingMethod\Helper\Data
     */
    private $printingMethodHelper;

    public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
								\Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
                                \Designnbuy\Extrafee\Helper\Data $helperData,
                                \Designnbuy\PrintingMethod\Helper\Data $printingMethodHelper
    )
    {
        $this->quoteValidator = $quoteValidator;
		$this->_priceCurrency = $priceCurrency;
        $this->helperData = $helperData;
        $this->printingMethodHelper = $printingMethodHelper;
    }

    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }



        //$enabled = $this->helperData->isModuleEnabled();
        //$minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        //$subtotal = $total->getTotalAmount('subtotal');
        //if ($enabled && $minimumOrderAmount <= $subtotal) {
        $fee = 0;
        $items = $quote->getAllItems();
        $compareId = [];
        foreach ($items as $item) {
            //if($item->getParentItemId() != '') continue;
            if($item->getParentItem()) continue;
            $qty = $item->getQty();
            //$infoBuyRequest = $item->getOptionByCode('info_buyRequest');
           // $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue());
            $itemOptions = (array) json_decode($item->getOptionByCode('info_buyRequest')->getValue(), true);

            if(isset($itemOptions) && isset($itemOptions['printingMethod']) && !empty($itemOptions['printingMethod'])){
                $printingMethodData = $itemOptions['printingMethod'];
                if(is_string($itemOptions['printingMethod'])){
                    $printingMethodData = json_decode($itemOptions['printingMethod'], true);
                }

               // $printingMethodData = json_decode($itemOptions['printingMethod'], true);


                if (isset($printingMethodData) && !empty($printingMethodData)) {
                    $printingPrices = $this->printingMethodHelper->calculatePrintingMethodPrice($printingMethodData, $qty);
                    if(isset($printingPrices) && !empty($printingPrices)){
                        //echo "artWorkSetupPrice".$printingPrices['artWorkSetupPrice'];
                        if (isset($itemOptions) && isset($itemOptions['currentTime']) && !in_array($itemOptions['currentTime'], $compareId)) {
                            $quote->setFee($printingPrices['artWorkSetupPrice']);
                            $fee += $printingPrices['artWorkSetupPrice'];
                            $compareId[] = $itemOptions['currentTime'];
                        }
                    }
                }
            }
        }
        //$fee = 10;
        //$fee = $this->helperData->getExtrafee();
        //Try to test with sample value


        $total->setTotalAmount('fee', $fee);
        $total->setBaseTotalAmount('fee', $fee);
        $total->setFee($fee);
        $quote->setFee($fee);
        $total->setGrandTotal($total->getGrandTotal());
		//}
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {

        $enabled = $this->helperData->isModuleEnabled();
        $minimumOrderAmount = $this->helperData->getMinimumOrderAmount();
        $subtotal = $quote->getSubtotal();
        $fee = $quote->getFee();

        $result = [];
        if (($minimumOrderAmount <= $subtotal) && $fee) {
            $result = [
                'code' => 'fee',
                'title' => $this->helperData->getFeeLabel(),
                'value' => $fee
            ];
        }
        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('Artwork Setup Charge');
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total)
    {
        $total->setFee(0);
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);

    }
}
