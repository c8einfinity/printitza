<?php
namespace Designnbuy\Merchandise\Model;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;
use Magento\Framework\App\Filesystem\DirectoryList;

class Merchandise extends \Magento\Framework\Model\AbstractModel
{
    const XML_COLOR_PICKER_TYPE = 'merchandise/configuration/element_color_picker_type';
    const XML_BG_COLOR_PICKER_TYPE = 'merchandise/configuration/bg_color_picker_type';
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * JSON Encoder instance
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    public $_jsonEncoder;

    /**
     * Catalog category factory
     *
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * Catalog product factory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Designidea factory
     *
     * @var \Designnbuy\ConfigArea\Model\ConfigAreaFactory
     */
    protected $_configAreaFactory;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogProduct = null;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;
    
    /**
     * @var OptionRepository
     */
    protected $productOptionsRepository;

    /**
     * @var \Magento\ConfigurableProduct\Helper\Data $imageHelper
     */
    protected $helper;

    /**
     * @var ConfigurableAttributeData
     */
    protected $configurableAttributeData;

    protected $_area = 'admin';

    protected $_product;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * Catalog product attribute backend tierprice
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice
     */
    protected $_productAttributeBackendSide;

    /**
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Product
     */
    protected $_printingMethodProduct;
    
    /**
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod
     */
    protected $_printingMethodResource;

    /**
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingmethodFactory;
    
    /**
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingQuantityRange;
    
    /**
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingSquareArea;
    
    /**
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingColorCounter;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var Stock
     */
    private $stockHelper;

    /**
     * Catalog product visibility
     *
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelperFactory;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbBaseHelper;

    const XML_BASE_UNIT = 'merchandise/configuration/base_unit';

    /**
     * @var array
     */
    protected $product = [];

    /**
     * @var \Designnbuy\Customer\Model\DesignFactory
     */

    private $designFactory;

    /**
     * Designidea factory.
     *
     * @var \Designnbuy\Designidea\Model\DesignideaFactory
     */
    protected $_designideaFactory;

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $quoteItemFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Product
     */
    protected $colorProduct;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    protected $colorID = '';

    protected $sizeID = '';

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    protected $storeId = 0;

    /**
     * Catalog category factory
     *
     * @var \Designnbuy\Color\Model\Color
     */
    protected $_color;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Collection
     */
    protected $_colorCollection;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;
    /**
     * @var \Designnbuy\Designidea\Helper\Data
     */
    protected $_designideaCategoryHelper;


    /**
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\ConfigurableProduct\Helper\Data $helper
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     * @param ConfigurableAttributeData $configurableAttributeData
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelperFactory
     */

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\ConfigurableProduct\Helper\Data $helper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        ConfigurableAttributeData $configurableAttributeData,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Designnbuy\Merchandise\Model\ResourceModel\Product\Attribute\Backend\Side $productAttributeSide,
        \Designnbuy\PrintingMethod\Model\PrintingMethodFactory $printingmethodFactory,
        \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Product $printingMethodProduct,
        \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod $printingMethodResource,
        \Designnbuy\PrintingMethod\Model\QuantityRange $printingQuantityRange,
        \Designnbuy\PrintingMethod\Model\SquareArea $printingSquareArea,
        \Designnbuy\PrintingMethod\Model\ColorCounter $printingColorCounter,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Designnbuy\ConfigArea\Model\ConfigAreaFactory $_configAreaFactory,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Color\Model\ResourceModel\Color\Product $colorProduct,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Designnbuy\Color\Model\Color $color,
        \Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory $colorCollectionFactory,
        \Designnbuy\Designidea\Helper\Data $designideaCategoryHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Model\Product\Option\Repository $repository
    ) {
        $this->_scopeConfig    = $scopeConfig;
        $this->_jsonEncoder    = $jsonEncoder;
        $this->_storeManager = $storeManager;
        $this->_categoryFactory = $categoryFactory;
        $this->productRepository = $productRepository;
        $this->catalogProduct = $catalogProduct;
        $this->helper = $helper;
        $this->configurableAttributeData = $configurableAttributeData;
        $this->swatchHelper = $swatchHelper;
        $this->_productAttributeBackendSide = $productAttributeSide;
        $this->_printingmethodFactory = $printingmethodFactory;
        $this->_printingMethodProduct = $printingMethodProduct;
        $this->_printingMethodResource = $printingMethodResource;
        $this->_printingQuantityRange = $printingQuantityRange;
        $this->_printingSquareArea = $printingSquareArea;
        $this->_printingColorCounter = $printingColorCounter;
        $this->categoryRepository = $categoryRepository;
        $this->stockHelper = $stockHelper;
        $this->stockRegistry = $stockRegistry;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->_configAreaFactory = $_configAreaFactory;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->designFactory = $designFactory;
        $this->_outputHelper = $outputHelper;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->cart = $cart;
        $this->_designideaFactory = $designideaFactory;
        $this->colorProduct = $colorProduct;
        $this->jsonHelper = $jsonHelper;
        $this->_color = $color;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->mediaConfig = $mediaConfig;
        $this->_colorCollectionFactory = $colorCollectionFactory;
        $this->_designideaCategoryHelper = $designideaCategoryHelper;
        $this->priceHelper = $priceHelper;
        $this->productOptionsRepository = $repository;
    }


    protected function getStoreConfig($field) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($field, $storeScope);
    }

    public function getStoreId() {
        if($this->storeId){
            $storeId = $this->storeId;
        } else {
            $storeId = $this->_storeManager->getStore()->getStoreId();
        }
        return $storeId;
    }

    public function getWebsiteId() {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    public function getStoreCode() {
        return $this->_storeManager->getStore()->getCode();
    }

    protected function getRootCategoryId()
    {
        $store = $this->getStoreId();
        //return $rootCategoryId = $this->_storeManager->getStore($store)->getRootCategoryId();
        return $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();//without assign store id
    }

    public function getDefaultProduct($website = '')
    {
        $customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();
        $categoryId = $this->getRootCategoryId();
        $category = $this->categoryRepository->get($categoryId);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $products */
        $collection = $category->getProductCollection();
        $collection->addAttributeToSelect(array('*'));
        $collection->addFieldToSelect('position');
        $collection->addFieldToFilter('is_customizable', 1);
        $collection->addFieldToFilter('status', 1);
        $collection->addAttributeToFilter('attribute_set_id',$customProductAttributeSetId);
        $collection->addAttributeToFilter('type_id', ['simple', 'configurable']);

        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        if($website != '' && $website != 0){
            $collection->addWebsiteFilter($website);
        }
        $this->stockHelper->addInStockFilterToCollection($collection);

        $collection->addAttributeToSort('entity_id','ASC')->load();
        //$collection = $collection->getFirstItem();
        //$productCollectionArray = $collection->getData();

        $productId = 0;

        foreach ($collection->getData() as $product) {
            $productId = $product['entity_id'];
            break;
        }

        return $productId;
    }

    public function getCategories($store = ''){
        $categoryId = $this->getRootCategoryId();
        $categoryModel = $this->_categoryFactory->create();

        if($store){
            $this->storeId = $store;
            $categoryModel->setStoreId($store);
        } else {
            $categoryModel->setStoreId($this->getStoreId());
        }

        $categoryModel->load($categoryId);
        $categoryArray = $this->getChildCategories($categoryModel);
        $categoryJson = $this->_jsonEncoder->encode(isset($categoryArray['children']) ? $categoryArray['children'] : []);
        return $categoryJson;
    }

    protected function getChildCategories($categoryModel, $level = 0)
    {
        $item = [];

        $customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();
        $collection = $categoryModel->getProductCollection();

        $collection->addAttributeToSelect(array('*'));
        $collection->addFieldToSelect('position');
        $collection->addFieldToFilter('is_customizable', 1);
        $collection->addFieldToFilter('status', 1);
        $collection->addAttributeToFilter('attribute_set_id',$customProductAttributeSetId);
        $collection->addAttributeToFilter('type_id', ['simple', 'configurable']);
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        
        $this->stockHelper->addInStockFilterToCollection($collection);

        $products = $this->getCategoryProducts($categoryModel->getId());

        if($products <= 0){
           // return;
        }
        $item['name'] = $categoryModel->getName().'('.$collection->count().')';


        $item['id'] = $categoryModel->getId();
        if ((int)$categoryModel->getChildrenCount() > 0) {
            $item['children'] = [];
        }

        if ($categoryModel->hasChildren()) {
            $item['children'] = [];
            $children = $categoryModel->getChildren();
            if(!empty($children)) {
                $childCategories = explode(',', $children);
                foreach ($childCategories as $child) {
                    $categoryModel = $this->_categoryFactory->create();

                    $categoryModel->setStoreId($this->getStoreId());
                    $categoryModel->load($child);
                    $products = $this->getCategoryProducts($categoryModel->getId());
                    if($products <= 0){
                        continue;
                    }
                    if(count($products) > 0){
                        $item['children'][] = $this->getChildCategories($categoryModel, $level + 1);
                    }
                }
            }
        }

        return $item;
    }

    public function getCategoryProducts($categoryId, $website = '')
    {
        $customProductAttributeSetId = $this->dnbBaseHelper->getCustomProductAttributeSetId();
        $category = $this->categoryRepository->get($categoryId);

        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $products */
        $collection = $category->getProductCollection();
        $collection->addAttributeToSelect(array('*'));
        $collection->addFieldToSelect('position');
        $collection->addFieldToFilter('is_customizable', 1);
        $collection->addFieldToFilter('status', 1);
        $collection->addAttributeToFilter('attribute_set_id',$customProductAttributeSetId);
        $collection->addAttributeToFilter('type_id', ['simple', 'configurable']);
        
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());

        $this->stockHelper->addInStockFilterToCollection($collection);

        $products = [];
        foreach ($collection as $product) {
            $stockItem = $this->stockRegistry->getStockItem(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );
            if($product->getIsSalable() && $stockItem->getIsInStock())
            {
                $imageUrl = $this->imageHelperFactory->create()->init($product, 'product_page_image_small')->setImageFile($product->getSmallImage())->resize(380)->getUrl();
                $products[] = [
                    'name' => $product->getName(),
                    'id' => $product->getEntityId(),
                    'code' => $product->getSku(),
                    'shortDesc' => htmlspecialchars($product->getShortDescription()),
                    'longDesc' => htmlspecialchars($product->getDescription()),
                    'image' => $imageUrl,
                    'designidea_id' => $product->getDesignideaId(),
                ];
            }
        }

        return $products;
    }
    /**
     * Retrieve currently viewed product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->__product);
        }
        return $this->getData('product');
    }

    public function getProductData($request, $area = 'admin')
    {
        $productId = $request['id'];
        if($request && !empty($request) && !empty($request['store'])){
            $this->storeId = $request['store'];
        } else if($this->getStoreId()){
            $this->storeId = $this->getStoreId();
        }
        $this->_area = $area;
        $this->__product = $this->productRepository->getById($productId, false, $this->storeId);

        $this->getProductDetail();
        $this->getProductOptions();
        $this->getSidesConfiguration();
        $this->getBackground($request);

        if($this->__product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE){
            $this->getConfigurableAttributesData($this->__product);
        } else {
            $this->product['attributes'] = [];
        }

        $this->getRelatedPrintingMethods();

        //$request['design_id'] = 4;
        if(isset($request['design_id']) && $request['design_id'] != ''){
            $this->product['design'] = $this->getCustomerDesign($request['design_id']);
            
            //to get saved design selected custom option
            if (isset($this->product['design']['options'])) {
                if (isset($this->product['design']['options']['customOptions']) && !empty($this->product['design']['options']['customOptions'])) {
                    $this->product['selectedOptions'] = $this->product['design']['options']['customOptions'];
                    $this->product['postedOptions'] = $this->product['design']['options']['customOptions'];
                }
            }
        } else if(isset($request['item_id']) && $request['item_id'] != ''){
            $this->product['design'] = $this->getQuoteItemDesign($request['item_id']);
            if(isset($this->product['design']['options'])  && !empty($this->product['design']['options'])){
                $this->product['selectedOptions'] = $this->product['design']['options'];
                $this->product['postedOptions'] = $this->product['design']['options'];
            }
        } else if(isset($request['designidea_id']) && $request['designidea_id'] != ''){
            $this->product['design'] = $this->getDesignIdeaDesign($request);
            $this->product['design']['design_name'] = "";
        } else if(isset($request['jobId']) && $request['jobId'] != ''){
            $this->product['design'] = $this->getJobDesign($request['jobId']);
            //to get job design selected custom option
            if(isset($this->product['design']['customOptions']) && !empty($this->product['design']['customOptions'])){
                $this->product['selectedOptions'] = $this->product['design']['customOptions'];
                $this->product['postedOptions'] = $this->product['design']['customOptions'];
            }
        }  else if(isset($request['ddid']) && $request['ddid'] != ''){
            $this->product['design'] = $this->getDesignIdeaDesign($request['ddid']);
        } else{
            $this->product['design'] = [];//$this->getProductDesign($this->__product);
        }

        return $this->product;
    }

    protected function getProductDetail()
    {
        $product = $this->getProduct();
        $this->product['productID'] = $product->getEntityId();
        $this->product['name'] = $product->getName();
        $this->product['type'] = $product->getTypeId();
        $this->product['code'] = $product->getSku();
        $this->product['shortDesc'] = htmlspecialchars($product->getShortDescription());
        $this->product['longDesc'] = htmlspecialchars($product->getDescription());
        //$this->product['multiColor'] = 'no';
        $isMulticolor = $product->getIsMulticolor();
        if($isMulticolor == '1')
            $this->product['multiColor'] = 'yes';
        else
            $this->product['multiColor'] = 'no';

        if($product->getBaseUnit() != '') {
            $this->product['baseUnit'] = $product->getBaseUnit();
        } else {
            $this->product['baseUnit'] = $this->getStoreConfig(self::XML_BASE_UNIT);
        }

        /*if($this->_area == 'admin'){
            $this->product['noofSides'] = 1;
        }else{
            $this->product['noofSides'] = $product->getNoOfSides();
        }*/

        $this->product['noofSides'] = $product->getNoOfSides();
        $this->product['printing_method_type'] = $product->getPricingLogic() != '' ? $product->getPricingLogic() : 1;

        /*For 3d Preview Start*/
        $mapImage = '';
        $model3d = '';
        $this->product['is_3d'] = ($product->getData('is_3d') == 1 ) ? 1 : 0;
        /*if($isMulticolor != 1) {
            if ($product->getMapImage() != 'no_selection' && $product->getMapImage() != '') {
                $mapImage = $product->getMapImage();
            }
            $this->product['map_image'] = $mapImage;
        }*/
        if ($product->getMapImage() != 'no_selection' && $product->getMapImage() != '') {
            $mapImage = $this->mediaConfig->getMediaUrl($product->getMapImage());
        }
        $this->product['map_image'] = $mapImage;

        if($product->getData('model_3d') != 'no_selection' && $product->getData('model_3d') != '') {
            $model3d = $this->mediaConfig->getMediaUrl($product->getData('model_3d'));
        }
        $this->product['model_3d'] = $model3d;

        if($product->getData('is_3d') == 1){
            $this->product['threed_configure_area'] = $product->getThreedConfigureArea();
        }
        /*For 3d Preview End*/
        $this->product['allow_text'] = ($product->getData('allow_text') == null) ? 1 : (int) $product->getData('allow_text');
        $this->product['allow_clipart'] = ($product->getData('allow_clipart') == null) ? 1 : (int) $product->getData('allow_clipart');
        $this->product['allow_qr_code'] = ($product->getData('allow_qr_code') == null) ? 1 : (int) $product->getData('allow_qr_code');
        $this->product['allow_image_upload'] = ($product->getData('allow_image_upload') == null) ? 1 : (int) $product->getData('allow_image_upload');
        $this->product['allow_name_number'] = ($product->getData('allow_name_number') == null) ? 0 : (int) $product->getData('allow_name_number');
        $this->product['allow_product'] = ($product->getData('allow_product') == null) ? 1 : (int) $product->getData('allow_product');
        $this->product['productDesignideaCount'] = count($this->_designideaCategoryHelper->getCategories($product->getEntityId()));
        $this->product['designidea_id'] = $product->getDesignideaId();
        if($product->getDesignideaId() != null || $product->getDesignideaId() != ''){
            $this->product['designidea_id_design'] = $this->getDesignIdeaDesign($product->getDesignideaId());
        }

        $this->product['allowed_to_ordermode'] = ($product->getData('hide_addtocart') == 1) ? false : true;
        $this->product['show_price'] = ($product->getData('hide_price') == 1) ? false : true;
        
        return $this->product;
    }
    
    public function getProductOptions()
    {
        $product = $this->getProduct();
        $i = 0;
        $price = 1;
        $sideId = null;
        $sideOptionId = null;
        $options = [];
        $backgroundColor = [];
        foreach ($product->getOptions() as $_option) {
            //$dependentJs = '';
            if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) {
                $dependentJs = '';
                $values = $_option->getValues();
                $valuesArray = array();
                $count = 1;
                $default = false;
                $temp = false;

                if ($_option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD && $_option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) {

                    foreach ($values as $_value) {

                        $valuesArray[] = array(
                            "option_type_id" => $_value['option_type_id'],
                            "mageworx_option_type_id" => $_value['mageworx_option_type_id'],
                            "option_id" => $_value['option_id'],
                            "sku" => $_value['sku'],
                            "default_title" => $_value['title'],
                            "designtool_title" => isset($_value['designtool_title']) && $_value['designtool_title'] != '' ? $_value['designtool_title'] : $_value['title'],
                            "priceModifier" => '',
                            "sort_order" => $_value['sort_order'],
                            "mask_image" => '',
                            "overlay_image" => '',
                            "count" => $count,
                            // "disabled" => ($enabledDependent && $_option->getIsDependent()) ? 'disabled="disabled"' : '',
                            // "default" => $_value['default'] == 1 ? true : false
                            "default" => $default
                        );
                    }
                }
                
                $option = array(
                    "id" => $_option->getId(),
                    "type" => $_option->getType(),
                    "title" => $_option->getTitle(),
                    "mageworx_option_id" => $_option->getData('mageworx_option_id'),
                    "is_require" => $_option->getData('is_require'),
                    "sort_order" => $_option->getSortOrder(),
                    "values" => $valuesArray,
                    "dependentJs" => $dependentJs,
                    //"dependentJs" => $dependentJs,
                    "disabled" =>  false,
                    //"disabled" =>  ($enabledDependent && $_option->getIsDependent()? true : false),
                );

                $options[]= $option;
                $i++;
            }
        }

        $this->product['options'] = $options;
        $this->product['dependentData'] = $this->getDependentData();
        return $this->product;
    }

    public function getBackground($request)
    {
        /****************** Start Background ********************/
        $productId = $request['id'];
        if($request && !empty($request) && !empty($request['store'])){
            $this->storeId = $request['store'];
        } else if($this->getStoreId()){
            $this->storeId = $this->getStoreId();
        }
        
        $product = $this->productRepository->getById($productId, false, $this->storeId);

        $colorPickerType = 'Full';
        if($product->getElementColorPickerType() != '' && $product->getElementColorPickerType() != null) {
            $colorPickerValue = $product->getElementColorPickerType();
            if($colorPickerValue == 1){
                $colorPickerType = "Full";
            } else{
                $colorPickerType = "Printable";
            }
        } else {
            if($this->getStoreConfig(self::XML_COLOR_PICKER_TYPE) == 1){
                $colorPickerType = "Full";
            } else{
                $colorPickerType = "Printable";
            }
        }
        $bgcolorPickerType = 'Full';
        if($product->getBgColorPickerType() != '' && $product->getBgColorPickerType() != null) {
            if($product->getBgColorPickerType() == 1){
                $bgcolorPickerType = "Full";
            } else if($product->getBgColorPickerType() == 3){
                $bgcolorPickerType = "Printable";
            } else {
                $bgcolorPickerType = "Custom";
            }
        } else {
            if( $this->getStoreConfig(self::XML_BG_COLOR_PICKER_TYPE) == 1){
                $bgcolorPickerType = "Full";
            } else {
                $bgcolorPickerType = "Printable";
            }
        }
        $printableColors = $this->_color->getRelatedProductColors($product);
        $printableBackgroundColors = $this->_color->getRelatedBackgroundProductColors($product);

        if(count($printableColors) <= 0 ){
            $colorPickerType = 'Full';
        }

        $this->product['colors'] = $printableColors;
        $this->product['background_colors'] = $printableBackgroundColors;

        $this->product['colorPickerMode'] = $colorPickerType;
        $this->product['bgcolorPickerMode'] = $bgcolorPickerType;
        $this->product['allow_background_color'] = ($product->getData('allow_background_color') == null) ? 0 : (int) $product->getData('allow_background_color');
        $this->product['allow_background_image'] = ($product->getData('allow_background_image') == null) ? 0 : (int) $product->getData('allow_background_image');
        

        $bgcolorText = "White(#FFFFFF)";
        $bgString = trim($bgcolorText);
        preg_match_all('/\(([^\)]*)\)/', $bgString, $aMatches);
        $color_name = explode('(#',$bgString);
        $this->product['bgcolor'] = $aMatches[1][0];
        $this->product['BgId'] = '';
        
        return $this->product;
        /****************** End Background **********************/
    }

    protected function getSidesConfiguration()
    {
        $this->product['sides'] = $this->_productAttributeBackendSide->getSidesData($this->getProduct());
        return $this->product;
    }

    protected function getConfigurableAttributesData($currentProduct)
    {
        $options = $this->helper->getOptions($currentProduct, $this->getAllowProducts());
        $this->getAttributesData($currentProduct, $options);
    }

    /**
     * Get product attributes
     *
     * @param Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesData(\Magento\Catalog\Model\Product $product, array $options = [])
    {
        $defaultValues = [];
        $attributes = [];
        $colorAttribute = '';
        $sizeAttribute = '';

        foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                if($productAttribute->getAttributeCode() == \Designnbuy\Merchandise\Helper\Data::COLOR_FIELD){
                    $colorAttribute = $productAttribute->getId();
                    $this->colorID = $colorAttribute;
                }else if($productAttribute->getAttributeCode() == \Designnbuy\Merchandise\Helper\Data::SIZE_FIELD){
                    $sizeAttribute = $productAttribute->getId();
                    $this->sizeID = $colorAttribute;
                }
                $attributeId = $productAttribute->getId();
                $attributes[$attributeId] = [
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $productAttribute->getStoreLabel($product->getStoreId()),
                    'options' => $attributeOptionsData,
                    'position' => $attribute->getPosition(),
                ];
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }
        $this->product['colorId'] = $colorAttribute;
        $this->product['sizeId'] = $sizeAttribute;
        $this->product['defaultValues'] = $defaultValues;
        $this->product['attributes'] = $attributes;
        return;
    }

    /**
     * @param Attribute $attribute
     * @param array $config
     * @return array
     */
    protected function getAttributeOptionsData($attribute, $config)
    {
        $attributeOptionsData = [];
        $productAttribute = $attribute->getProductAttribute();
        foreach ($attribute->getOptions() as $attributeOption) {
            $optionId = $attributeOption['value_index'];
            //$attributeOptionIds[] = $optionId;
            $swatches = $this->swatchHelper->getSwatchesByOptionsId(array($optionId));
            $values = [];
            if($productAttribute->getAttributeCode() == \Designnbuy\Merchandise\Helper\Data::COLOR_FIELD && isset($config[$attribute->getAttributeId()][$optionId])){
                $products = $config[$attribute->getAttributeId()][$optionId];
                foreach ($products as $productId) {
                    $simpleProduct = $this->productRepository->getById($productId);
                    $values = $this->_productAttributeBackendSide->getSidesData($simpleProduct);
                    if(!empty($values)){
                        break;
                    }
                }
            }
            if(!empty($values) && $this->getProduct()->getIsMulticolor() == 1){
                $value = $values;
            } else if(isset($swatches[$optionId])){
                $value = $swatches[$optionId]['value'];
            } else {
                $value = $attributeOption['label'];
                $colorArray = [];
                $colorArray = explode('(', $attributeOption['label']);
                if(isset($colorArray) && !empty($colorArray)){
                    $colorText = $colorArray[0];
                    $colorTemp = array_reverse($colorArray);
                    $colorName = explode(')', $colorTemp[0]);
                    $value = $colorName[0];
                }
            }

            if($value == ''){
                $value = $attributeOption['label'];
            }
            if(isset($config[$attribute->getAttributeId()][$optionId])){ //If there are no color specific products available in website
                $attributeOptionsData[] = [
                    'id' => $optionId,
                    'label' => $attributeOption['label'],
                    'products' => isset($config[$attribute->getAttributeId()][$optionId])
                        ? $config[$attribute->getAttributeId()][$optionId]
                        : [],
                    'type' => isset($swatches[$optionId])
                        ? $swatches[$optionId]['type']
                        : [], //textual swatch type = 0, swatch type with color number value = 1,swatch type with image = 2,
                    /*'value' => isset($swatches[$optionId])
                        ? $swatches[$optionId]['value']
                        : [],*/
                    'value' => $value,
                ];
            }

        }

        return $attributeOptionsData;
    }

    /**
     * @param int $attributeId
     * @param Product $product
     * @return mixed|null
     */
    protected function getAttributeConfigValue($attributeId, $product)
    {
        return $product->hasPreconfiguredValues()
            ? $product->getPreconfiguredValues()->getData('super_attribute/' . $attributeId)
            : null;
    }


    /**
     * Get Allowed Products
     *
     * @return \Magento\Catalog\Model\Product[]
     */
    public function getAllowProducts()
    {
        //if (!$this->hasAllowProducts()) {
            /*$skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();

            $products = $skipSaleableCheck ?
                $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null) :
                $this->getProduct()->getTypeInstance()->getSalableUsedProducts($this->getProduct(), null);
            $this->setAllowProducts($products);*/

            $products = [];
            $skipSaleableCheck = $this->catalogProduct->getSkipSaleableCheck();
            $allProducts = $this->getProduct()->getTypeInstance()->getUsedProducts($this->getProduct(), null);
            foreach ($allProducts as $product) {

                if ($product->isSaleable() && in_array($this->getWebsiteId(), $product->getWebsiteIds())) {
                    $products[] = $product;
                }
            }
            $this->setAllowProducts($products);
        //}
        return $this->getData('allow_products');
    }

    /**
     * Get Rule label by specified store
     *
     * @param \Magento\Store\Model\Store|int|bool|null $store
     * @return string|bool
     */
    public function getSideStoreLabel($sides)
    {
        $storeId = $this->getStoreCode();
        $labels = (array)$sides;

        if (isset($labels[$storeId])) {
            return $labels[$storeId];
        } elseif (isset($labels[0]) && $labels[0]) {
            return $labels[0];
        }

        return false;
    }

    public function getRelatedPrintingMethods()
    {
        $namePrice = 0;
        $numberPrice = 0;
        $product = $this->getProduct();
        $productId = $this->getProduct()->getId();
        $storeId = 0;
        if($product->getPricingLogic() == 1){
            $elementColorPickerType = $product->getElementColorPickerType();
            $printableColors = [];
            if($product->getElementColorPickerType() == 2){
                //$relatedColors = $this->colorProduct->getRelatedColors($product);//Product Specific Colors
                if($product->getColorCategory() != ''){
                    $categories[] = $product->getColorCategory();
                    $printableColors = $this->_color->getRelatedProductColors($product);
                    /*$relatedColors = $this->_colorCollectionFactory->create()
                        ->addActiveFilter()
                        ->addStoreFilter($this->getStoreId())
                        ->addCategoryFilter($categories);

                    foreach ($relatedColors as $relatedColor) {
                        $printableColors[] = [
                            'name' => $relatedColor->getTitle(),
                            'colorCode' => $relatedColor->getColorCode(),
                        ];
                    }*/
                }
            } else if($this->getStoreConfig(self::XML_COLOR_PICKER_TYPE) == 2){
                $elementColorPickerType = 2;
                if($product->getColorCategory() != ''){
                    $categories[] = $product->getColorCategory();
                    $printableColors = $this->_color->getRelatedProductColors($product);
                }
            }
            $namePrice = $this->priceHelper->currency((float)$this->dnbBaseHelper->getNamePrice(), true, false);
            $numberPrice = $this->priceHelper->currency((float)$this->dnbBaseHelper->getNumberPrice(), true, false);

            $printingMethods[] =[
                'name' => 'Standard',
                'printingMethodId' => 0,
                'pricingLogic' => 1,
                'printableColorType' => $elementColorPickerType != '' ? $elementColorPickerType : 1,
                'minQty' => 1,
                'maxQty' => 10000000,
                'isImageUpload' => 1,
                'isAlert' => 0,
                'alertMessage' => '',
                'printableColors' => $printableColors,
                'namePrice' => $namePrice,
                'numberPrice' => $numberPrice,
            ];
        } else {
            if($this->getStoreId()){
                $storeId = $this->getStoreId();
            }
            $relatedPrintingMethods = $this->_printingMethodProduct->getRelatedPrintingMethods($productId);
            $printingMethods = [];
            foreach ($relatedPrintingMethods as $relatedPrintingMethod) {
                
                if ($relatedPrintingMethod->getPricingLogic() == 1) {
                    
                    $printingPriceData = $this->_printingMethodResource->getQPrices($relatedPrintingMethod->getPrintingmethodId());

                } else if ($relatedPrintingMethod->getPricingLogic() == 2) {

                    $printingPriceData = $this->_printingMethodResource->getQCPrices($relatedPrintingMethod->getPrintingmethodId());

                } else if ($relatedPrintingMethod->getPricingLogic() == 3) {

                    $printingPriceData = $this->_printingMethodResource->getQAPrices($relatedPrintingMethod->getPrintingmethodId());

                }

                
                $printingMethod = $this->_printingmethodFactory->create()->load($relatedPrintingMethod->getPrintingmethodId());
                //echo "<pre>"; print_r($printingMethod->getData()); exit;
                $printingMethodPricingTable = [];

                if (!empty($printingPriceData)) {
                    
                    foreach ($printingPriceData as $key => $priceData) {
                        //$printingMethodPricingTable[$key]['printingmethod_name'] = $printingMethod->getTitle();
                        
                        if(isset($priceData['quantityrange_id'])){
                            if($priceData['quantityrange_id'] != ""){
                            $printingQuantityRange = $this->_printingQuantityRange->load($priceData['quantityrange_id']);
                                if(!empty($printingQuantityRange->getData()) && $printingQuantityRange->getTitle()){
                                    $printingMethodPricingTable[$key]['qty_range'] = $printingQuantityRange->getTitle();
                                }
                            }
                        }
                        
                        if(isset($priceData['colorcounter_id'])){
                            if($priceData['colorcounter_id'] != ""){
                            $printingColorRange = $this->_printingColorCounter->load($priceData['colorcounter_id']);
                                if(!empty($printingColorRange->getData()) && $printingColorRange->getTitle()){
                                    $printingMethodPricingTable[$key]['color_range'] = $printingColorRange->getTitle();
                                }
                            }
                        }
                        
                        if(isset($priceData['squarearea_id'])){
                            if($priceData['squarearea_id'] != ""){
                            $printingAreaRange = $this->_printingSquareArea->load($priceData['squarearea_id']);
                                if(!empty($printingAreaRange->getData()) && $printingAreaRange->getTitle()){
                                    $printingMethodPricingTable[$key]['area_range'] = $printingAreaRange->getTitle();
                                }
                            }
                        }
                        
                        if(isset($priceData[0])){
                            if($priceData[0] != ""){
                                $printingMethodPricingTable[$key]['side_1'] = $priceData[0];
                            }
                        }
                        if(isset($priceData[1])){
                            if($priceData[1] != ""){
                                $printingMethodPricingTable[$key]['side_2'] = $priceData[1];
                            }
                        }
                        if(isset($priceData[2])){
                            if($priceData[2] != ""){
                                $printingMethodPricingTable[$key]['side_3'] = $priceData[2];
                            }
                        }
                        if(isset($priceData[3])){
                            if($priceData[3] != ""){
                                $printingMethodPricingTable[$key]['side_4'] = $priceData[3];
                            }
                        }
                    }
                    //echo "<pre>"; print_r($printingMethodPricingTable);
                    //$this->product['printing_method_pricing_table'] = $printingMethodPricingTable;

                }
                
                $categories = $printingMethod->getCategories();
                $printableColors = [];
                /*Colors from category assigned to printing method*/
                $relatedColors = $this->_colorCollectionFactory->create()
                    ->distinct(true)
                    ->addActiveFilter()
                    ->addStoreFilter($this->getStoreId())
                    ->addCategoryFilter($categories);

                $relatedColors->getSelect()->group('main_table.color_id');

                foreach ($relatedColors as $relatedColor) {
                    $printableColors[] = [
                        'name' => $relatedColor->getTitle(),
                        'colorCode' => $relatedColor->getColorCode(),
                    ];
                }

                /*Colors added to printing method*/
                /*$relatedColors = $printingMethod->getRelatedColors();

                foreach ($relatedColors as $relatedColor) {
                    $printableColors[] = [
                        'name' => isset($relatedColor[$storeId])
                            ? $relatedColor[$storeId]
                            : $relatedColor[0],
                        'colorCode' => $relatedColor['color_code'],
                    ];
                }*/
                $namePrice = $this->priceHelper->currency((float)$printingMethod->getNamePrice(), true, false);
                $numberPrice = $this->priceHelper->currency((float)$printingMethod->getNumberPrice(), true, false);

                $printingMethods[] =[
                    'name' => $printingMethod->getStoreLabel($storeId) != ''
                        ? $printingMethod->getStoreLabel($storeId)
                        : $printingMethod->getTitle(),
                    'printingMethodId' => $printingMethod->getId(),
                    'pricingLogic' => $printingMethod->getPricingLogic(),
                    'printableColorType' => $printingMethod->getPrintableColors(),
                    'minQty' => $printingMethod->getMinQty(),
                    'maxQty' => $printingMethod->getMaxQty(),
                    'isImageUpload' => $printingMethod->getIsImageUpload(),
                    'isAlert' => $printingMethod->getIsAlert(),
                    'alertMessage' => $printingMethod->getAlertMessage(),
                    'printableColors' => $printableColors,
                    'namePrice' => ($printingMethod->getNamePrice() == null) ? 0 : $namePrice,
                    'numberPrice' => ($printingMethod->getNumberPrice() == null) ? 0 : $numberPrice,
                    'printing_method_pricing_table' => $printingMethodPricingTable
                ];
            }
            
        }
        $this->product['printingMethods'] = $printingMethods;
        //return $printingMethods;
    }

    public function getCustomerDesign($designId)
    {
        $designDir = $this->_outputHelper->getCustomerDesignsDir();
        $designFactory = $this->designFactory->create()->load($designId);

        $design = [];
        $design['design_id'] = $designFactory->getDesignId();
        $design['design_name'] = $designFactory->getDesignName();

        if($designFactory->getSvg() && $designFactory->getSvg() != ''){
            $svgs = [];
            $svgs = explode(',', $designFactory->getSvg());
            foreach ($svgs as $svg) {
                if(file_exists($designDir.$svg)){
                    $design['svg'][] = file_get_contents($designDir.$svg);
                }
            }

        }

        //$design['nameNumber'] = '';
        if($designFactory->getOptions() && $designFactory->getOptions() != ''){
            $options = $designFactory->getOptions();
            $optionsArray = (array) $this->jsonHelper->jsonDecode($options, true);
            $design['options'] = $optionsArray;
            $nameNumber = (array) $optionsArray['nameNumber'];
            if(isset($nameNumber['data']) && !empty($nameNumber['data']) && ($nameNumber['totalname'] > 0 || $nameNumber['totalnumber'] > 0)){
                $nameNumberFile = $nameNumber['data'];
                $nameNumberFileData = file_get_contents($designDir . $nameNumberFile);
                if ($nameNumberFileData != '' && $nameNumberFileData != 'undefined') {
                    //$design['nameNumber'] = json_decode($nameNumberFileData, true);
                    $design['nameNumber'] = $nameNumberFileData;
                }
            }
        }
        return $design;
    }
    public function getCurrentCustomerDesignSVG($designId)
    {
        $designDir = $this->_outputHelper->getCustomerDesignsDir();

        $design = [];
        if(isset($designId) && $designId != ''){
            $designModel = $this->designFactory->create();
            $designModel->load($designId);
            $options = $designModel->getOptions();

            $design['design_id'] = $designModel->getDesignId();
            $design['design_name'] = $designModel->getDesignName();
            $design['options'] = json_decode($options, true);
            $designSVGs = explode(",", $designModel->getSvg());

            if(isset($designSVGs) && !empty($designSVGs)){
                foreach ($designSVGs as $designSVG) {
                    if(file_exists($designDir.$designSVG)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$designSVG);
                        $design['svg'][] = $formatedSVG;
                    }
                }

            }
        }
        return $design;
    }

    public function getQuoteItemDesign($itemId)
    {
        /*$item = $this->quoteItemFactory->create();
        $item->load($itemId);*/
        $designDir = $this->_outputHelper->getCartDesignsDir();
        $design = [];
        $quoteItem = $this->cart->getQuote()->getItemById($itemId);
        
        if($quoteItem){
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');


            $itemOptions = (array) $this->jsonHelper->jsonDecode($quoteItem->getOptionByCode('info_buyRequest')->getValue());

            if(isset($itemOptions) && !empty($itemOptions)){
                $design['qty'] = $quoteItem->getQty();
            }
            if(isset($itemOptions) && !empty($itemOptions['super_attribute'])){
                $design['super_attribute'] = $itemOptions['super_attribute'];
                foreach ($itemOptions['super_attribute'] as $index => $value) {
                    if($this->colorID != '' && $this->colorID == $index){
                        $design['colorOptionId'] = $value;
                        $design['qty'] = $quoteItem->getQty();
                    } else {
                        $design['sizeOptionId'] = $value;
                    }
                }
            }

            if(isset($itemOptions) && !empty($itemOptions['config_area_ids'])){
                $design['config_area_ids'] = $itemOptions['config_area_ids'];
            }

            if(isset($itemOptions) && !empty($itemOptions['printingMethod'])){
                $design['printingMethod'] = json_decode($itemOptions['printingMethod']);
            }

            if(isset($itemOptions['options']) && !empty($itemOptions['options'])){
                $design['options'] = $itemOptions['options'];
            }

            if(isset($itemOptions['svg']) && !empty($itemOptions['svg'])){
                $svgs = [];
                $svgs = explode(',', $itemOptions['svg']);
                foreach ($svgs as $svg) {
                    if(file_exists($designDir.$svg)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$svg);
                        $design['svg'][] = $formatedSVG;
                    }
                }
            }
            //$design['nameNumber'] = [];
            if(isset($itemOptions['nameNumber']) && !empty($itemOptions['nameNumber'])){
                $nameNumber = (array) $itemOptions['nameNumber'];
                if(isset($nameNumber['data']) && !empty($nameNumber['data']) && ($nameNumber['totalname'] > 0 || $nameNumber['totalnumber'] > 0)){
                    $nameNumberFile = $nameNumber['data'];
                    if(file_exists($designDir.$nameNumberFile)) {
                        $nameNumberFileData = file_get_contents($designDir . $nameNumberFile);
                        if ($nameNumberFileData != '' && $nameNumberFileData != 'undefined') {
                            //$design['nameNumber'] = json_decode($nameNumberFileData, true);
                            $design['nameNumber'] = $nameNumberFileData;
                        }
                    }
                }
            }
        }
        return $design;
    }

    public function getDesignIdeaDesign($request)
    {
        $fromQuickedit = '';
        $quickEditDesigns = [];
        if(is_array($request)){
            $designId = $request['designidea_id'];
            if(array_key_exists('from_quickedit', $request) && $request['from_quickedit'] == 1){
                $fromQuickedit = $request['from_quickedit'];
                $quickEditDesigns = $request['quick_edit_designs'];
            }
        } else {
            $designId = $request;
        }
        $designDir = $this->_outputHelper->getDesignIdeaDesignsDir();
        $designFactory = $this->_designideaFactory->create();
        if($this->storeId){
            $storeId = $this->storeId;
        } else {
            $storeId = $this->getStoreId();
        }

        $designFactory->setStoreId($storeId);
        $designFactory->load($designId);

        $design = [];
        $design['design_id'] = $designFactory->getEntityId();
        $design['design_name'] = $designFactory->getTitle();
        $design['design_description'] = htmlspecialchars($designFactory->getDescription());

        if($designFactory->getOptions() != ''){
            $options = $this->jsonHelper->jsonDecode($designFactory->getOptions());
            if(isset($options)){
                if(isset($options) && $options['color_id'] != ''){
                    $design['colorOptionId'] = $options['color_id'];
                }

                if(isset($options) && $options['config_area_ids'] != ''){
                    $design['config_area_ids'] = $options['config_area_ids'];
                }
            }
        }
        
        if($fromQuickedit && !empty($quickEditDesigns)){
            $designDir = $this->_outputHelper->getCartDesignsDir();
            foreach ($quickEditDesigns as $quickEditDesign) {
                if($quickEditDesign != '' && file_exists($designDir.$quickEditDesign)){
                    $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$quickEditDesign);
                    $design['design_id'] = '';
                    $design['design_name'] = '';
                    $design['svg'][] = $formatedSVG;
                    unlink($designDir.$quickEditDesign);
                }
            }
        } else {
            if($designFactory->getSvg() && $designFactory->getSvg() != ''){
                $svgs = [];
                $svgs = explode(',', $designFactory->getSvg());
                foreach ($svgs as $svg) {
                    if(file_exists($designDir.$svg)){
                        $formatedSVG = $this->_outputHelper->getFormatedSVG($designDir.$svg);
                        $design['svg'][] = $formatedSVG;//file_get_contents($designDir.$svg);
                    }
                }
            }
        }

        return $design;
    }

    public function getJobDesign($jobId)
    {
        $design = [];
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        if ($objectManager->create('\Magento\Framework\Module\Manager')->isEnabled('Designnbuy_CorporateJobRequest')) {

            $designDir = $this->_outputHelper->getJobDesignsDir();
            $designFactory = $objectManager->create('\Designnbuy\CorporateJobRequest\Model\Corporatejob')->load($jobId);

            $design['design_id'] = $designFactory->getJobId();

            if($designFactory->getOptions() != ''){
                $options = $this->jsonHelper->jsonDecode($designFactory->getOptions());
                if(isset($options)){
                    if(isset($options['customOptions'])){
                        $design['customOptions'] = $options['customOptions'];
                    }
                    if(isset($options) && isset($options['color_id']) && !empty($options['color_id']) && $options['color_id'] != ''){
                        $design['colorOptionId'] = $options['color_id'];
                    }

                    if(isset($options) && isset($options['config_area_ids']) && !empty($options['config_area_ids']) && $options['config_area_ids'] != ''){
                        $design['config_area_ids'] = $options['config_area_ids'];
                    }
                }
            }

            if($designFactory->getSvg() && $designFactory->getSvg() != ''){
                $svgs = [];
                $svgs = explode(',', $designFactory->getSvg());
                foreach ($svgs as $svg) {
                    if(file_exists($designDir.$svg)){
                        $design['svg'][] = file_get_contents($designDir.$svg);
                    }
                }
            }
        }

        return $design;
    }


    public function isQuickEditEnable($_product)
    {
        $productId = $_product->getId();
        $store = $_product->getStoreId();
        $merchandisePersonalizeOption = $this->getMerchandiseOptions($productId, $store, $_product);

        if (($merchandisePersonalizeOption == 2 || $merchandisePersonalizeOption == 3)
            && $_product->getDesignideaId() != '' && $_product->getIsCustomizable()) {

            $designIdea = $this->getDesignIdeaDesign($_product->getDesignideaId());
            if(isset($designIdea) && !empty($designIdea)){
                if(isset($designIdea['svg']) && $designIdea['svg'] != ''){
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }

    public function getMerchandiseOptions($productId, $store, $_product)
    {
       return $_product->getResource()->getAttributeRawValue($productId, 'merchandise_personalize_option', $store);
    }

    /**
     * Get config json data
     * @return string JSON
     */
    public function getDependentData()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $this->modelConfig = $objectManager->get('\MageWorx\OptionDependency\Model\Config');
        $data = [
            'optionParents' => $this->getOptionParents(),
            'valueParents' => $this->getValueParents(),
            'optionChildren' => $this->getValueParents(),
            'valueChildren' => $this->getValueChildren(),
            'optionTypes' => $this->getOptionTypes(),
            'optionRequiredConfig' => $this->getOptionsRequiredParam(),
            'andDependencyOptions' => $this->getAndDependencyOptions()
        ];

        return $data;
    }

    /**
     * Get 'child_option_id' - 'parent_option_type_id' pairs in json
     * @return array
     */
    public function getOptionParents()
    {
        return $this->modelConfig->getOptionParents($this->getProduct()->getId());
    }

    /**
     * Get 'child_option_type_id' - 'parent_option_type_id' pairs in json
     * @return array
     */
    public function getValueParents()
    {
        return $this->modelConfig->getValueParents($this->getProduct()->getId());
    }

    /**
     * Get 'parent_option_type_id' - 'child_option_id' pairs in json
     * @return array
     */
    public function getOptionChildren()
    {
        return $this->modelConfig->getOptionChildren($this->getProduct()->getId());
    }

    /**
     * Get 'parent_option_type_id' - 'child_option_type_id' pairs in json
     * @return array
     */
    public function getValueChildren()
    {
        return $this->modelConfig->getValueChildren($this->getProduct()->getId());
    }

    /**
     * Get option types ('mageworx_option_id' => 'type') in json
     * @return array
     */
    public function getOptionTypes()
    {
        return $this->modelConfig->getOptionTypes($this->getProduct()->getId());
    }

    /**
     * Get options  types ('mageworx_option_id' => 'type') in json
     * @return array
     */
    public function getAndDependencyOptions()
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        return $this->modelConfig->getAndDependencyOptions($product);
    }

    /**
     * Returns array with key -> mageworx option ID , value -> is option required
     * Used in the admin area during order creation to add a valid css classes when toggle option based on dependencies
     *
     * @return array
     */
    public function getOptionsRequiredParam()
    {
        $config = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        /** @var \Magento\Catalog\Model\Product\Option[] $options */
        $options = $product->getOptions();
        foreach ($options as $option) {
            // Use raw option from the repository because product options miss original required parameter
            // Sometime it is set as false where in the original option it is true
            /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $rawOption */
            $rawOption = $this->productOptionsRepository->get($product->getSku(), $option->getId());
            $config[$option->getId()] = (bool)$rawOption->getIsRequire();
            $config[$option->getData('mageworx_option_id')] = (bool)$rawOption->getIsRequire();
        }

        return $config;
    }

}