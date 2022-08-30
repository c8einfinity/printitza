<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\Report\Column\Renderer;

use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Class RateValue
 * @author BrainActs Core Team <support@brainacts.com>
 */
class RateValue extends AbstractRenderer
{

    /**
     * @var int
     */
    protected $_defaultWidth = 100;

    /**
     * Currency objects cache
     *
     * @var \Magento\Framework\DataObject[]
     */
    public static $_currencies = [];

    /**
     * Application object
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * @var \Magento\Directory\Model\Currency\DefaultLocator
     */
    public $currencyLocator;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    public $defaultBaseCurrency;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    public $localeCurrency;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Currency\DefaultLocator $currencyLocator,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->localeCurrency = $localeCurrency;
        $this->storeManager = $storeManager;
        $this->currencyLocator = $currencyLocator;

        $defaultBaseCurrencyCode = $this->_scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            'default'
        );
        $this->defaultBaseCurrency = $currencyFactory->create()->load($defaultBaseCurrencyCode);
    }

    /**
     * Get price currency
     *
     * @return \Magento\Framework\Pricing\PriceCurrencyInterface
     *
     * @deprecated
     */
    private function getPriceCurrency()
    {
        if ($this->priceCurrency === null) {
            $this->priceCurrency = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Pricing\PriceCurrencyInterface::class
            );
        }
        return $this->priceCurrency;
    }

    /**
     * Renders grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {

        $value = $this->_getValue($row);
        $rate = $row->getData('rate_type');

        switch ($rate) {
            case 1:
                $rate = __('%');
                break;
            case 2:
                $price = $this->_getValue($row) ? $this->_getValue($row) : $this->getColumn()->getDefault();
                $displayPrice = $this->getPriceCurrency()->convertAndFormat($price, false);
                return $displayPrice;
        }
        return $value . $rate;
    }

    /**
     * Returns currency code, false on error
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    protected function _getCurrencyCode($row)
    {
        if ($code = $this->getColumn()->getCurrencyCode()) {
            return $code;
        }
        if ($code = $row->getData($this->getColumn()->getCurrency())) {
            return $code;
        }

        return $this->currencyLocator->getDefaultCurrency($this->_request);
    }

    /**
     * Get rate for current row, 1 by default
     *
     * @param \Magento\Framework\DataObject $row
     * @return float|int
     */
    protected function _getRate($row)
    {
        if ($rate = $this->getColumn()->getRate()) {
            return floatval($rate);
        }
        if ($rate = $row->getData($this->getColumn()->getRateField())) {
            return floatval($rate);
        }
        return $this->defaultBaseCurrency->getRate($this->_getCurrencyCode($row));
    }

    /**
     * Returns HTML for CSS
     *
     * @return string
     */
    public function renderCss()
    {
        return parent::renderCss() . ' a-right';
    }
}
