<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product tier price backend attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Pricecalculator\Model\Product\Attribute\Backend;
use Magento\Catalog\Api\Data\ProductInterface;

class SquarePrice extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;
    /**
     * Catalog product attribute backend tierprice
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    protected $_productAttributeBackendSquarePrice;

    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice $productAttributeTierprice
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Designnbuy\Pricecalculator\Model\ResourceModel\Product\Attribute\Backend\SquarePrice $productAttributeTierprice
    ) {
        $this->_productAttributeBackendSquarePrice = $productAttributeTierprice;
        $this->_storeManager = $storeManager;
        $this->_groupManagement = $groupManagement;

    }

    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_height_width_square_price', 'value_id');
    }

    /**
     * Retrieve resource instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    protected function _getResource()
    {
        return $this->_productAttributeBackendSquarePrice;
    }

    /**
     * Add price qty to unique fields
     *
     * @param array $objectArray
     * @return array
     */
    protected function _getAdditionalUniqueFields($objectArray)
    {
        $uniqueFields['qty'] = $objectArray['price_qty'] * 1;
        return $uniqueFields;
    }

    /**
     * @inheritdoc
     */
    protected function getAdditionalFields($objectArray)
    {
        //$percentageValue = $this->getPercentage($objectArray);
        return [
           // 'value' => $percentageValue ? null : $objectArray['price'],
            'value' => $objectArray['price'],
           // 'percentage_value' => $percentageValue ?: null,
        ];
    }

    /**
     * Error message when duplicates
     *
     * @return \Magento\Framework\Phrase
     */
    protected function _getDuplicateErrorMessage()
    {
        return __('We found a duplicate website, tier price, customer group and quantity.');
    }

    /**
     * Whether tier price value fixed or percent of original price
     *
     * @param \Magento\Catalog\Model\Product\Type\Price $priceObject
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isTierPriceFixed();
    }

    /**
     * By default attribute value is considered non-scalar that can be stored in a generic way
     *
     * @return bool
     */
    public function isScalar()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function validate($object)
    {
        $attribute = $this->getAttribute();
        $priceRows = $object->getData($attribute->getName());
        $priceRows = array_filter((array)$priceRows);

        foreach ($priceRows as $priceRow) {
            $percentage = $this->getPercentage($priceRow);
            if ($percentage !== null && (!$this->isPositiveOrZero($percentage) || $percentage > 100)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Percentage value must be a number between 0 and 100.')
                );
            }
        }

        return parent::validate($object);
    }

    /**
     * @inheritdoc
     */
    protected function validatePrice(array $priceRow)
    {
        if (!$this->getPercentage($priceRow)) {
            parent::validatePrice($priceRow);
        }
    }

    /**
     * Check whether price has percentage value.
     *
     * @param array $priceRow
     * @return null
     */
    private function getPercentage($priceRow)
    {
        return isset($priceRow['percentage_value']) && is_numeric($priceRow['percentage_value'])
            ? $priceRow['percentage_value']
            : null;
    }

    /**
     * Retrieve websites currency rates and base currency codes
     *
     * @return array
     */
    protected function _getWebsiteCurrencyRates()
    {
        if (is_null($this->_rates)) {
            $this->_rates = [];
            $baseCurrency = $this->_config->getValue(
                \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
                'default'
            );
            foreach ($this->_storeManager->getWebsites() as $website) {
                /* @var $website \Magento\Store\Model\Website */
                if ($website->getBaseCurrencyCode() != $baseCurrency) {
                    $rate = $this->_currencyFactory->create()->load(
                        $baseCurrency
                    )->getRate(
                        $website->getBaseCurrencyCode()
                    );
                    if (!$rate) {
                        $rate = 1;
                    }
                    $this->_rates[$website->getId()] = [
                        'code' => $website->getBaseCurrencyCode(),
                        'rate' => $rate,
                    ];
                } else {
                    $this->_rates[$website->getId()] = ['code' => $baseCurrency, 'rate' => 1];
                }
            }
        }
        return $this->_rates;
    }


    /**
     * Prepare group prices data for website
     *
     * @param array $priceData
     * @param string $productTypeId
     * @param int $websiteId
     * @return array
     */
    public function preparePriceData(array $priceData, $productTypeId, $websiteId)
    {

        $data = [];
        /*$rates = $this->_getWebsiteCurrencyRates();
        $price = $this->_catalogProductType->priceFactory($productTypeId);
        foreach ($priceData as $v) {
            if (!array_filter($v)) {
                continue;
            }
            $key = implode('-', array_merge([$v['cust_group']], $this->_getAdditionalUniqueFields($v)));
            if ($v['website_id'] == $websiteId) {
                $data[$key] = $v;
                $data[$key]['website_price'] = $v['price'];
            } elseif ($v['website_id'] == 0 && !isset($data[$key])) {
                $data[$key] = $v;
                $data[$key]['website_id'] = $websiteId;
                if ($this->_isPriceFixed($price)) {
                    $data[$key]['price'] = $v['price'] * $rates[$websiteId]['rate'];
                    $data[$key]['website_price'] = $v['price'] * $rates[$websiteId]['rate'];
                }
            }
        }*/

        return $data;
    }

    /**
     * Assign group prices to product data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $data = $this->_getResource()->loadPriceData(
            $object->getId(),
            $this->getWebsiteId($object->getStoreId())
        );
        $this->setPriceData($object, $data);

        return $this;
    }

    /**
     * @param int $storeId
     * @return int|null
     */
    private function getWebsiteId($storeId)
    {
        $websiteId = null;
        if ($this->getAttribute()->isScopeGlobal()) {
            $websiteId = 0;
        } elseif ($storeId) {
            $websiteId = $this->_storeManager->getStore($storeId)->getWebsiteId();
        }
        return $websiteId;
    }

    /**
     * @param \Magento\Catalog\Model\Product $object
     * @param array $priceData
     */
    public function setPriceData($object, $priceData)
    {
        $priceData = $this->modifyPriceData($object, $priceData);
        $websiteId = $this->getWebsiteId($object->getStoreId());
        if (!$object->getData('_edit_mode') && $websiteId) {
            $priceData = $this->preparePriceData($priceData, $object->getTypeId(), $websiteId);
        }

        $object->setData($this->getAttribute()->getName(), $priceData);
        $object->setOrigData($this->getAttribute()->getName(), $priceData);

        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);
    }

    /**
     * Perform price modification
     *
     * @param \Magento\Catalog\Model\Product $object
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function modifyPriceData($object, $data)
    {
        /** @var array $priceItem */
        foreach ($data as $key => $priceItem) {
            if (array_key_exists('price', $priceItem)) {
                $data[$key]['website_price'] = $priceItem['price'];
            }
            if ($priceItem['all_groups']) {
                $data[$key]['cust_group'] = $this->_groupManagement->getAllCustomersGroup()->getId();
            }
        }
        return $data;
    }

    /**
     * After Save Attribute manipulation
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function afterSave($object)
    {
        $websiteId = $this->_storeManager->getStore($object->getStoreId())->getWebsiteId();
        $isGlobal = $this->getAttribute()->isScopeGlobal() || $websiteId == 0;

        $priceRows = $object->getData($this->getAttribute()->getName());
        if (null === $priceRows) {
            return $this;
        }

        $priceRows = array_filter((array)$priceRows);

        $old = [];
        $new = [];

        // prepare original data for compare
        $origPrices = $object->getOrigData($this->getAttribute()->getName());
        if (!is_array($origPrices)) {
            $origPrices = [];
        }
        foreach ($origPrices as $data) {
            if ($data['website_id'] > 0 || $data['website_id'] == '0' && $isGlobal) {
                $key = implode(
                    '-',
                    array_merge(
                        [$data['website_id'], $data['cust_group']],
                        $this->_getAdditionalUniqueFields($data)
                    )
                );
                $old[$key] = $data;
            }
        }

        // prepare data for save
        foreach ($priceRows as $data) {
            $hasEmptyData = false;
            foreach ($this->_getAdditionalUniqueFields($data) as $field) {
                if (empty($field)) {
                    $hasEmptyData = true;
                    break;
                }
            }

            if ($hasEmptyData || !isset($data['cust_group']) || !empty($data['delete'])) {
                continue;
            }
            if ($this->getAttribute()->isScopeGlobal() && $data['website_id'] > 0) {
                continue;
            }
            if (!$isGlobal && (int)$data['website_id'] == 0) {
                continue;
            }

            $key = implode(
                '-',
                array_merge([$data['website_id'], $data['cust_group']], $this->_getAdditionalUniqueFields($data))
            );

            $useForAllGroups = $data['cust_group'] == $this->_groupManagement->getAllCustomersGroup()->getId();
            $customerGroupId = !$useForAllGroups ? $data['cust_group'] : 0;
            $new[$key] = array_merge(
                $this->getAdditionalFields($data),
                [
                    'website_id' => $data['website_id'],
                    'all_groups' => $useForAllGroups ? 1 : 0,
                    'customer_group_id' => $customerGroupId,
                    'value' => isset($data['price']) ? $data['price'] : null,
                ],
                $this->_getAdditionalUniqueFields($data)
            );
        }

        $delete = array_diff_key($old, $new);
        $insert = array_diff_key($new, $old);
        $update = array_intersect_key($new, $old);

        $isChanged = false;
        $productId = $object->getData($this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField());

        if (!empty($delete)) {
            foreach ($delete as $data) {
                $this->_getResource()->deletePriceData($productId, null, $data['price_id']);
                $isChanged = true;
            }
        }

        if (!empty($insert)) {
            foreach ($insert as $data) {
                $price = new \Magento\Framework\DataObject($data);
                $price->setData(
                    $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField(),
                    $productId
                );
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }

        if (!empty($update)) {
            $isChanged |= $this->updateValues($update, $old);
        }

        if ($isChanged) {
            $valueChangedKey = $this->getAttribute()->getName() . '_changed';
            $object->setData($valueChangedKey, 1);
        }

        return $this;
    }

    /**
     * @param array $valuesToUpdate
     * @param array $oldValues
     * @return boolean
     */
    protected function updateValues(array $valuesToUpdate, array $oldValues)
    {
        $isChanged = false;
        foreach ($valuesToUpdate as $key => $value) {
            if ($oldValues[$key]['price'] != $value['value']) {
                $price = new \Magento\Framework\DataObject(
                    [
                        'value_id' => $oldValues[$key]['price_id'],
                        'value' => $value['value']
                    ]
                );
                $this->_getResource()->savePriceData($price);
                $isChanged = true;
            }
        }
        return $isChanged;
    }

    /**
     * Retrieve data for update attribute
     *
     * @param  \Magento\Catalog\Model\Product $object
     * @return array
     */
    public function getAffectedFields($object)
    {
        $data = [];
        $prices = (array)$object->getData($this->getAttribute()->getName());
        $tableName = $this->_getResource()->getMainTable();
        foreach ($prices as $value) {
            $data[$tableName][] = [
                'attribute_id' => $this->getAttribute()->getAttributeId(),
                'entity_id' => $object->getId(),
                'value_id' => $value['price_id'],
            ];
        }

        return $data;
    }

    /**
     * Get resource model instance
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\GroupPrice\AbstractGroupPrice
     */
    public function getResource()
    {
        return $this->_getResource();
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     */
    private function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\EntityManager\MetadataPool::class);
        }
        return $this->metadataPool;
    }
}
