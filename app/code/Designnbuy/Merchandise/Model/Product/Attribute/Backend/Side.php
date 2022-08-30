<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Catalog product tier price backend attribute model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Merchandise\Model\Product\Attribute\Backend;

class Side extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * Catalog product attribute backend tierprice
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    protected $_productAttributeBackendSide;


    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;

    /** @var  LoggerInterface */
    protected $logger;
    /**
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Customer\Api\GroupManagementInterface $groupManagement
     * @param \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice $productAttributeTierprice
     * @param \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection
     */
    public function __construct(
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Customer\Api\GroupManagementInterface $groupManagement,
        \Designnbuy\Merchandise\Model\ResourceModel\Product\Attribute\Backend\Side $productAttributeSide,
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Psr\Log\LoggerInterface $logger

    ) {
        $this->_productAttributeBackendSide = $productAttributeSide;
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->logger = $logger;
    }
    /**
     * Initialize connection and define main table
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('designnbuy_merchandise_product_sides_configuration', 'value_id');
    }

    protected function _getResource()
    {
        return $this->_productAttributeBackendSide;
    }
    /**
     * Assign group prices to product data
     *
     * @param \Magento\Catalog\Model\Product $object
     * @return $this
     */
    public function afterLoad($object)
    {
        $sideData = $this->_productAttributeBackendSide->loadSideData(
            $object->getId()
        );

        $object->setData($this->getAttribute()->getName(), $sideData);

        $object->setOrigData($this->getAttribute()->getName(), $sideData);
        
        $valueChangedKey = $this->getAttribute()->getName() . '_changed';
        $object->setOrigData($valueChangedKey, 0);
        $object->setData($valueChangedKey, 0);

        return $this;
    }


    

    /**
     * @param \Magento\Framework\DataObject $object
     *
     * @return $this
     */
    public function beforeSave($object)
    {
        return parent::beforeSave($object);
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
        $sidesRows = $object->getData($this->getAttribute()->getName());

        if (null === $sidesRows) {
            return $this;
        }

        $sidesRows = array_filter((array)$sidesRows);

        $old = [];
        $new = [];
        // prepare original data for compare
        $origSides = $object->getOrigData($this->getAttribute()->getName());

        if (!is_array($origSides)) {
            $origSides = [];
        }

        // prepare data for save
        if(isset($sidesRows) && !empty($sidesRows)){
            foreach ($sidesRows as $key => $data) {
                $storesTitles = $this->_composeStoreLabels($data);

                $new[] = [
                    'value_id' => isset($data['value_id']) ? $data['value_id'] : null,//$data['value_id'],
                    'side_id' => $key,
                    'product_id' => $object->getId(),
                    'label' => $data['label'],
                    'image' =>  $this->_getImageFields($data),
                    'color_image' =>  $this->_getColorImageFields($data),
                    'mask_image' =>  $this->_getMaskImageFields($data),
                    'overlay_image' =>  $this->_getOverlayImageFields($data),
                    'configure_areas' =>  $this->_getConfigureAreasFields($data),
                    'position' =>  $key,
                    'store_labels' =>  $storesTitles,
                ];

            }
        }


        foreach ($origSides as $key => $data) {
            $old[$key] = $data;
        }
        $insert = array_diff_key($new, $old);
        $update = array_intersect_key($new, $old);
        $isChanged = false;
        $insert = array_diff_key($new, $old);
        if (!empty($insert)) {
            foreach ($insert as $data) {
                $side = new \Magento\Framework\DataObject($data);
                $this->_getResource()->saveSidesData($side);
                $isChanged = true;
            }
        }
        if (!empty($update)) {
            foreach ($update as $data) {
                $side = new \Magento\Framework\DataObject($data);
                $this->_getResource()->saveSidesData($side);
                $isChanged = true;
            }
        }

        if ($isChanged) {
            $valueChangedKey = $this->getAttribute()->getName() . '_changed';
            $object->setData($valueChangedKey, 1);
        }

        return $this;
    }
    /**
     * Compose stores cache
     *
     * This cache is used to quickly retrieve store ID when handling locale-specific clipart titles
     *
     * @param string[] $validFields list of valid CSV file fields
     * @return array
     */
    protected function _composeStoreLabels($validFields)
    {
        $storesCache = [];
        foreach ($this->_publicStores as $store) {
            //$storeCode = $fieldsKey[$index];
            if(isset($validFields[$store->getCode()])){
                $storesCache[$store->getId()] = $validFields[$store->getCode()];
            }
        }

        return $storesCache;
    }
    protected function _getImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data) && isset($data['image'])){
                $image = $data['image'];
                if(isset($image['file']) && is_array($image['file'])) {
                    return $imageName = $image['file'][0]['file'];
                }
            }
        }
        return $imageName;
    }

    protected function _getColorImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data) && isset($data['color'])){
                $image = $data['color'];
                if(isset($image['file']) && is_array($image['file'])) {
                    return $imageName = $image['file'][0]['file'];
                }
            }
        }
        return $imageName;
    }

    protected function _getMaskImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data) && isset($data['mask'])){
                $image = $data['mask'];
                if(isset($image['file']) && is_array($image['file'])) {
                    return $imageName = $image['file'][0]['file'];
                }
            }
        }
        return $imageName;
    }

    protected function _getOverlayImageFields($data)
    {
        $imageName = '';
        if(isset($data)){
            if(is_array($data) && isset($data['overlay'])){
                $image = $data['overlay'];
                if(isset($image['file']) && is_array($image['file'])) {
                    return $imageName = $image['file'][0]['file'];
                }
            }
        }
        return $imageName;
    }

    protected function _getConfigureAreasFields($data)
    {
        $configureArea = '';
        if(isset($data)){
            if(is_array($data) && isset($data['configure_areas'])){
                $configure_areas = $data['configure_areas'];
                if(is_array($configure_areas) && isset($configure_areas)) {
                    return $configureArea = implode(',',$configure_areas);
                }
            }
        }
        return $configureArea;
    }

}
