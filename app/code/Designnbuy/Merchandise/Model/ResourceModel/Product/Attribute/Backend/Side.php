<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Merchandise\Model\ResourceModel\Product\Attribute\Backend;

/**
 * Catalog product tier price backend attribute model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Side extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Catalog product attribute backend tierprice
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    protected $_datahelper;
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $dateTime;

    /**
     * Magento string lib
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;
    /**
     * Collection of publicly available stores
     *
     * @var \Magento\Store\Model\ResourceModel\Store\Collection
     */
    protected $_publicStores;
    /**
     * ConfigArea Factory.
     *
     * @var \Designnbuy\Merchandise\Model\ConfigAreaFactory
     */
    protected $_configAreaFactory;
    /**
     * ConfigArea Collection Factory.
     *
     * @var \Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory
     */
    protected $_configAreaCollectionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Designnbuy\Merchandise\Helper\Data $datahelper,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Store\Model\ResourceModel\Store\Collection $storeCollection,
        \Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory $_configAreaCollectionFactory,
        \Designnbuy\ConfigArea\Model\ConfigAreaFactory $_configAreaFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
        $this->string = $string;
        $this->_helper = $datahelper;
        $this->_urlBuilder = $urlBuilder;
        $this->_publicStores = $storeCollection->setLoadDefault(false);
        $this->_configAreaFactory = $_configAreaFactory;
        $this->_configAreaCollectionFactory = $_configAreaCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->mediaConfig = $mediaConfig;
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


    /**
     * Save tier price object
     *
     * @param \Magento\Framework\DataObject $priceObject
     * @return \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    public function saveSidesData(\Magento\Framework\DataObject $sideObject)
    {
        $connection = $this->getConnection();
        $data = $this->_prepareDataForTable($sideObject, $this->getMainTable());

        if (!empty($data[$this->getIdFieldName()])) {
            $valueId = $data[$this->getIdFieldName()];
            $where = [
                $connection->quoteInto($this->getIdFieldName() . ' = ?', $data[$this->getIdFieldName()]),
                $connection->quoteInto('product_id = ?', $sideObject->getProductId()),
            ];
            unset($data[$this->getIdFieldName()]);
            $connection->update($this->getMainTable(), $data, $where);
            $this->saveStoreLabels($valueId, $sideObject->getStoreLabels());
        } else {
            $connection->insert($this->getMainTable(), $data);
            $valueId = $connection->lastInsertId($this->getMainTable());
            $this->saveStoreLabels($valueId, $sideObject->getStoreLabels());
        }
        return $this;
    }


    /**
     * Load Sides for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return array
     */
    public function loadSideData($productId)
    {
        $connection = $this->getConnection();

        $columns = [
            'value_id' => $this->getIdFieldName(),
            'label' => 'label',
            'side_id' => 'side_id',
            'product_id' => 'product_id',
            'image' => 'image',
            'color_image' => 'color_image',
            'mask_image' => 'mask_image',
            'overlay_image' => 'overlay_image',
            'configure_areas' => 'configure_areas',
        ];

        $productIdFieldName = 'product_id';

        $select = $connection->select()
            ->from($this->getMainTable(),$columns)
            ->where("{$productIdFieldName} = ?", $productId);

        $sideData = [];
        $sides = [];
        $sides = $connection->fetchAll($select);
        if(!empty($sides)){
            foreach ($sides as $key => $side){

                $data = [
                    'value_id' => $side['value_id'],
                    'side_id' => $key,
                    'label' => $side['label'],
                    'image' =>  $this->addImageFile($side),
                    'color' =>  $this->addColorImageFile($side),
                    'mask' =>  $this->addMaskImageFile($side),
                    'overlay' =>  $this->addOverlayImageFile($side),
                    'configure_areas' =>  explode(',',$side['configure_areas']),
                ];
                /*Get Side Configure Areas*/
                $configAreaCollection = $this->_configAreaCollectionFactory->create();
                $configAreaCollection->addFieldToFilter('product_id', $productId);
                $configAreaCollection->addFieldToFilter('side_id', $side['value_id']);

                $configAreas = [];
                $sideConfigAreas = [];
                foreach ($configAreaCollection as $configArea) {
                    $configAreas[$configArea->getConfigareaId()] = json_decode($configArea->getArea(), true);
                    $configAreaModel = $this->_configAreaFactory->create()->load($configArea->getConfigareaId());
                    if($this->getStoreId()){
                        $areaTitle = $configAreaModel->getStoreLabel($this->getStoreId());
                    } else {
                        $areaTitle = $configAreaModel->getTitle();
                    }
                    $sideConfigAreas[$configAreaModel->getConfigareaId()]['title'] = $areaTitle;
                    $area = [];
                    if($configArea->getArea()){
                        $area = json_decode($configArea->getArea(), true);
                    } else {
                        $area = json_decode($configAreaModel->getArea(), true);
                    }
                    $sideConfigAreas[$configAreaModel->getConfigareaId()]['area'] = $area;
                }

                $data['area'] = $configAreas;
                $data['sides_area'] = $sideConfigAreas;

                $storeLabels = $this->getStoreLabels($side['value_id']);
                $sideData[] = array_merge($data, $storeLabels);
            }
        }

        if(empty($sideData)){
            $sideData = $this->_helper->getSideOptions();
        }
        return $sideData;
    }

    /**
     * Add Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function addImageFile(array $data)
    {
        $imageData = [];
        if (is_array($data) && isset($data['image'])) {
            $image = $data['image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::IMAGE_PATH, $image);
            if ($this->_helper->fileExists($file)) {

                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::IMAGE_PATH . $image;

                $imageData['file'][0] = [
                    'file' => $image,
                    'name' => $this->_helper->getFileFromPathFile($image),
                    'size' => $this->_helper->getFileSize($file),
                    'status' => 'old',
                    'url' => $imageUrl,
                ];
                $imageData['url'] = $imageUrl;
                return $imageData;
            }
        }
        return '';
    }
    /**
     * Add Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function addColorImageFile(array $data)
    {
        $imageData = [];
        if (is_array($data) && isset($data['color_image'])) {
            $image = $data['color_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::COLOR_PATH, $image);
            if ($this->_helper->fileExists($file)) {

                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::COLOR_PATH . $image;

                $imageData['file'][0] = [
                    'file' => $image,
                    'name' => $this->_helper->getFileFromPathFile($image),
                    'size' => $this->_helper->getFileSize($file),
                    'status' => 'old',
                    'url' => $imageUrl,
                ];
                $imageData['url'] = $imageUrl;
                //$data['image'] = $imageData;
                return $imageData;
            }
        }
        return '';
    }
    /**
     * Add Mask Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function addMaskImageFile(array $data)
    {
        $imageData = [];
        if (is_array($data) && isset($data['mask_image'])) {
            $image = $data['mask_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::MASK_PATH, $image);
            if ($this->_helper->fileExists($file)) {
                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::MASK_PATH . $image;

                $imageData['file'][0] = [
                    'file' => $image,
                    'name' => $this->_helper->getFileFromPathFile($image),
                    'size' => $this->_helper->getFileSize($file),
                    'status' => 'old',
                    'url' => $imageUrl,
                ];
                $imageData['url'] = $imageUrl;
                return $imageData;
            }
        }
        return '';
    }
    /**
     * Add Mask Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function addOverlayImageFile(array $data)
    {
        $imageData = [];
        if (is_array($data) && isset($data['overlay_image'])) {
            $image = $data['overlay_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::OVERLAY_PATH, $image);
            if ($this->_helper->fileExists($file)) {
                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::OVERLAY_PATH . $image;

                $imageData['file'][0] = [
                    'file' => $image,
                    'name' => $this->_helper->getFileFromPathFile($image),
                    'size' => $this->_helper->getFileSize($file),
                    'status' => 'old',
                    'url' => $imageUrl,
                ];
                $imageData['url'] = $imageUrl;
                return $imageData;
            }
        }
        return '';
    }
    /**
     * @return string
     */
    protected function getProductIdFieldName()
    {
        $table = $this->getTable('catalog_product_entity');
        $indexList = $this->getConnection()->getIndexList($table);
        return $indexList[$this->getConnection()->getPrimaryKeyName($table)]['COLUMNS_LIST'][0];
    }

    /**
     * Save sides labels for different store views
     *
     * @param int $clipartId
     * @param array $labels
     * @throws \Exception
     * @return $this
     */
    public function saveStoreLabels($valueId, $labels)
    {
        $deleteByStoreIds = [];
        $table = $this->getTable('designnbuy_merchandise_product_sides_configuration_label');
        $connection = $this->getConnection();

        $data = [];
        foreach ($labels as $storeId => $label) {
            if ($this->string->strlen($label)) {
                $data[] = ['value_id' => $valueId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $connection->beginTransaction();
        try {
            if (!empty($data)) {
                $connection->insertOnDuplicate($table, $data, ['label']);
            }

            if (!empty($deleteByStoreIds)) {
                $connection->delete($table, ['value_id=?' => $valueId, 'store_id IN (?)' => $deleteByStoreIds]);
            }
        } catch (\Exception $e) {
            $connection->rollback();
            throw $e;
        }
        $connection->commit();

        return $this;
    }

    /**
     * Get all existing sides labels
     *
     * @param int $clipartId
     * @return array
     */
    public function getStoreLabels($sideId)
    {
        $select = $this->getConnection()->select()->from(
            $this->getTable('designnbuy_merchandise_product_sides_configuration_label'),
            ['store_id', 'label']
        )->where(
            'value_id = :value_id'
        );

        $storeLabels = [];
        $storeLabels = $this->getConnection()->fetchPairs($select, [':value_id' => $sideId]);

        $labels = [];
        if(is_array($storeLabels) && !empty($storeLabels)){
            foreach ($this->_publicStores as $store) {
                if(isset($storeLabels[$store->getId()]) && $storeLabels[$store->getId()] != ''){
                    $labels[$store->getCode()] = $storeLabels[$store->getId()];
                }
            }
        }
        return $labels;
    }

    /**
     * Load Sides for product
     *
     * @param int $productId
     * @param int $websiteId
     * @return array
     */
    public function getSidesData($simpleProduct)
    {
        $productId = $simpleProduct->getId();
        $connection = $this->getConnection();

        $columns = [
            'value_id' => $this->getIdFieldName(),
            'label' => 'label',
            'side_id' => 'side_id',
            'product_id' => 'product_id',
            'image' => 'image',
            'color_image' => 'color_image',
            'mask_image' => 'mask_image',
            'overlay_image' => 'overlay_image',
            'configure_areas' => 'configure_areas',
        ];

        $productIdFieldName = 'product_id';

        $select = $connection->select()
            ->from($this->getMainTable(),$columns)
            ->where("{$productIdFieldName} = ?", $productId)
            ->where("image != ?", '');

        $sideData = [];
        $sides = [];
        $sides = $connection->fetchAll($select);
        if(!empty($sides)){
            foreach ($sides as $key => $side){
                $storeLabels = $this->getStoreLabels($side['value_id']);
                
                $sideLabel = $side['label'];
                if(isset($storeLabels) && !empty($storeLabels) && isset($storeLabels[$this->getStoreCode()]) && !empty($storeLabels[$this->getStoreCode()])){
                    $sideLabel = $storeLabels[$this->getStoreCode()];
                }

                $data = [
                    'label' =>  $sideLabel,
                    'image' =>  $this->getSideImage($side),
                    'color' =>  $this->getColorImage($side),
                    'mask' =>  $this->getMaskImage($side),
                    'overlay' =>  $this->getOverlayImage($side),
                    'configure_areas' =>  explode(',',$side['configure_areas']),
                ];

                if ($simpleProduct->getMapImage() != 'no_selection' && $simpleProduct->getMapImage() != '') {
                    $data['map_image'] = $this->mediaConfig->getMediaUrl($simpleProduct->getMapImage());
                }

                $configAreas = [];
                $sideConfigAreas = [];

                /*Get Side Configure Areas*/
                $configAreaCollection = $this->_configAreaCollectionFactory->create();
                $configAreaCollection->addFieldToFilter('product_id', $productId);
                $configAreaCollection->addFieldToFilter('side_id', $side['value_id']);
                if($configAreaCollection->getSize() > 0){
                    foreach ($configAreaCollection as $configArea) {
                        $configAreas[$configArea->getConfigareaId()] = json_decode($configArea->getArea(), true);
                        $configAreaModel = $this->_configAreaFactory->create()->load($configArea->getConfigareaId());
                        $areaTitle = '';
                        if($this->getStoreId()){
                            $areaTitle = $configAreaModel->getStoreLabel($this->getStoreId());
                        } else {
                            $areaTitle = $configAreaModel->getTitle();
                        }
                        if(!$areaTitle || $areaTitle == ''){
                            $areaTitle = $configAreaModel->getTitle();
                        }
                        $sideConfigAreas[$configAreaModel->getConfigareaId()]['title'] = $areaTitle;
                        $area = [];
                        if($configArea->getArea()){
                            $area = json_decode($configArea->getArea(), true);
                        } else {
                            $area = json_decode($configAreaModel->getArea(), true);
                        }
                        $sideConfigAreas[$configAreaModel->getConfigareaId()]['area'] = $area;
                    }
                } else {
                    $areasIds = explode(',',$side['configure_areas']);
                    if(isset($areasIds) && !empty($areasIds)){
                        foreach ($areasIds as $areasId){
                            $configAreaModel = $this->_configAreaFactory->create()->load($areasId);
                            $areaTitle = '';
                            if($this->getStoreId()){
                                $areaTitle = $configAreaModel->getStoreLabel($this->getStoreId());
                            } else {
                                $areaTitle = $configAreaModel->getTitle();
                            }

                            if(!$areaTitle || $areaTitle == ''){
                                $areaTitle = $configAreaModel->getTitle();
                            }
                            $sideConfigAreas[$areasId]['title'] = $areaTitle;
                            $area = [];
                            if($configAreaModel->getArea()){
                                $area = json_decode($configAreaModel->getArea(), true);
                            }
                            $sideConfigAreas[$areasId]['area'] = $area;
                        }
                    }
                }

                //$data['area'] = $configAreas;
                $data['sides_area'] = $sideConfigAreas;
                $sideData[] = $data;
            }
        }

        return $sideData;
    }

    /**
     * Add Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function getSideImage(array $data)
    {
        if (is_array($data) && isset($data['image'])) {
            $image = $data['image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::IMAGE_PATH, $image);
            if ($this->_helper->fileExists($file)) {

                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::IMAGE_PATH . $image;

                return $imageUrl;
            }
        }
        return '';
    }

    /**
     * Add Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function getColorImage(array $data)
    {
        if (is_array($data) && isset($data['color_image'])) {
            $image = $data['color_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::COLOR_PATH, $image);
            if ($this->_helper->fileExists($file)) {

                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::COLOR_PATH . $image;

                return $imageUrl;
            }
        }
        return '';
    }
    /**
     * Add Mask Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function getMaskImage(array $data)
    {
        if (is_array($data) && isset($data['mask_image'])) {
            $image = $data['mask_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::MASK_PATH, $image);
            if ($this->_helper->fileExists($file)) {
                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::MASK_PATH . $image;

                return $imageUrl;
            }
        }
        return '';
    }
    /**
     * Add Mask Image info into $sideData
     *
     * @param array $linkData
     * @return array
     */
    protected function getOverlayImage(array $data)
    {
        if (is_array($data) && isset($data['overlay_image'])) {
            $image = $data['overlay_image'];
            $file = $this->_helper->getFilePath(\Designnbuy\Merchandise\Helper\Data::OVERLAY_PATH, $image);
            if ($this->_helper->fileExists($file)) {
                $imageUrl = $this->_urlBuilder->getBaseUrl(
                        ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
                    ) . \Designnbuy\Merchandise\Helper\Data::OVERLAY_PATH . $image;

                return $imageUrl;
            }
        }
        return '';
    }

    /**
     * Get store id
     *
     * @return int Store id
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getStoreId();
    }

    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }
}
