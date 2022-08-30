<?php
namespace Designnbuy\Canvas\Model;

use Magento\ConfigurableProduct\Model\ConfigurableAttributeData;


class Canvas extends \Magento\Framework\Model\AbstractModel
{
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
     * @var \Magento\ConfigurableProduct\Helper\Data $imageHelper
     */
    protected $helper;

    protected $storeId = 0;

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
     * @var \Designnbuy\PrintingMethod\Model\PrintingMethodFactory
     */
    protected $_printingmethodFactory;

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
     * @var \Designnbuy\Customer\Model\DesignFactory
     */

    private $designFactory;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbBaseHelper;

    const XML_BASE_UNIT = 'canvas/configuration/base_unit';

    const XML_COLOR_PICKER_TYPE = 'canvas/configuration/element_color_picker_type';
    const XML_BG_COLOR_PICKER_TYPE = 'canvas/configuration/bg_color_picker_type';

    /**
     * @var array
     */
    protected $product = [];

    /**
     * Catalog category factory
     *
     * @var \Designnbuy\Color\Model\Color
     */
    protected $_color;

    /**
     * @var \Designnbuy\Base\Helper\Output
     */

    private $_outputHelper;

    /**
     * Designidea factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Media\Config
     */
    protected $mediaConfig;

    /**
     * @var OptionRepository
     */
    protected $productOptionsRepository;

    /**
     * @var ConfigModel
     */
    protected $modelConfig;

    /**
     * @var TemplateHelper
     */
    protected $templateHelper;

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
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Catalog\Helper\ImageFactory $imageHelperFactory,
        \Designnbuy\ConfigArea\Model\ConfigAreaFactory $_configAreaFactory,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        \Designnbuy\Color\Model\Color $color,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Magento\Framework\App\Request\Http $request,
        \Designnbuy\Pricecalculator\Helper\Data $pricecalculatorHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Designnbuy\Template\Helper\Data $templateHelper,
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
        $this->categoryRepository = $categoryRepository;
        $this->stockHelper = $stockHelper;
        $this->stockRegistry = $stockRegistry;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->_configAreaFactory = $_configAreaFactory;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->designFactory = $designFactory;
        $this->_color = $color;
        $this->_outputHelper = $outputHelper;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->cart = $cart;
        $this->_templateFactory = $templateFactory;
        $this->jsonHelper = $jsonHelper;
        $this->mediaConfig = $mediaConfig;
        $this->request = $request;
        $this->pricecalculatorHelper = $pricecalculatorHelper;
        $this->templateHelper = $templateHelper;
        $this->productOptionsRepository = $repository;
    }


    protected function getStoreConfig($field) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->_scopeConfig->getValue($field, $storeScope);
    }

    public function getStoreId() {
        return $this->_storeManager->getStore()->getStoreId();
    }

    public function getStoreCode() {
        return $this->_storeManager->getStore()->getStoreCode();
    }

    protected function getRootCategoryId()
    {
        $store = $this->getStoreId();
        //return $rootCategoryId = $this->_storeManager->getStore($store)->getRootCategoryId();
        return $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();//without assign store id
    }

    public function getCategories(){
        $categoryId = $this->getRootCategoryId();
        $categoryModel = $this->_categoryFactory->create();
        $categoryModel->setStoreId(0);
        $categoryModel->load($categoryId);
        $categoryArray = $this->getChildCategories($categoryModel);
        $categoryJson = $this->_jsonEncoder->encode(isset($categoryArray['children']) ? $categoryArray['children'] : []);
        return $categoryJson;
    }

    protected function getChildCategories($categoryModel, $level = 0)
    {
        $item = [];
        $item['name'] = $categoryModel->getName();
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
                    $categoryModel->setStoreId(0);
                    $categoryModel->load($child);
                    $item['children'][] = $this->getChildCategories($categoryModel, $level + 1);
                }
            }
        }

        return $item;
    }

    public function getCategoryProducts($categoryId)
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
                    'productID' => $product->getEntityId(),
                    'code' => $product->getSku(),
                    'shortDesc' => htmlspecialchars($product->getShortDescription()),
                    'longDesc' => htmlspecialchars($product->getDescription()),
                    'thumbImage' => $imageUrl,
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
            $this->setData('product', $this->_product);
        }
        return $this->getData('product');
    }

    public function getProductData($productId, $area = 'admin')
    {
        $this->_area = $area;
        $this->_product = $this->productRepository->getById($productId);
        
        $this->getProductDetail();
        $this->getProductOptions();
        /*if($area == 'admin'){
            $this->getProductOptions();
        }*/

        return $this->product;
    }

    protected function getProductDetail()
    {
        $product = $this->getProduct();

        $this->product['name'] = $product->getName();
        $this->product['id'] = $product->getEntityId();
        $this->product['type'] = $product->getTypeId();
        $this->product['sku'] = $product->getSku();
        $this->product['weight'] = $product->getSku();
        $this->product['product_url'] = $product->getProductUrl();
        $this->product['status'] = $product->getStatus();
        $this->product['shortDescription'] = htmlspecialchars($product->getShortDescription());
        $this->product['description'] = htmlspecialchars($product->getDescription());
        $this->product['image'] = '';


        if($product->getBaseUnit() != '') {
            //$this->product['baseUnit'] = $this->dnbBaseHelper->getUnit($product->getBaseUnit());
            $this->product['baseUnit'] = $product->getBaseUnit();
        } else {
            $this->product['baseUnit'] = $this->getStoreConfig(self::XML_BASE_UNIT);
        }

        /*if($this->_area == 'admin'){
            $this->product['noOfSides'] = 1;
        }else{
            $this->product['noOfSides'] = $product->getNoOfPages();
        }*/
        $this->product['noOfSides'] = $product->getNoOfPages();

        $width = $product->getWidth();
        $height = $product->getHeight();
        $this->product['size'] = $width.'x'.$height;


        $bleedMargin = array();
        $safeMargin = array();

        $bleedMargin = array(
            $product->getTopBleedMargin()  ? $product->getTopBleedMargin() : ($this->getStoreConfig('canvas/configuration/top_bleed_margin') ? $this->getStoreConfig('canvas/configuration/top_bleed_margin') : 0),
            $product->getRightBleedMargin()  ? $product->getRightBleedMargin() : ($this->getStoreConfig('canvas/configuration/right_bleed_margin') ? $this->getStoreConfig('canvas/configuration/right_bleed_margin') : 0),
            $product->getBottomBleedMargin() ? $product->getBottomBleedMargin() : ($this->getStoreConfig('canvas/configuration/bottom_bleed_margin') ? $this->getStoreConfig('canvas/configuration/bottom_bleed_margin') : 0),
            $product->getLeftBleedMargin()  ?  $product->getLeftBleedMargin() : ($this->getStoreConfig('canvas/configuration/left_bleed_margin') ? $this->getStoreConfig('canvas/configuration/left_bleed_margin') : 0),
        );
        $this->product['bleedMargin'] = $bleedMargin;

        $safeMargin = array(
            $product->getTopSafeMargin()  ? $product->getTopSafeMargin() : ($this->getStoreConfig('canvas/configuration/top_safe_margin') ? $this->getStoreConfig('canvas/configuration/top_safe_margin') : 0),
            $product->getRightSafeMargin()  ? $product->getRightSafeMargin() : ($this->getStoreConfig('canvas/configuration/right_safe_margin') ? $this->getStoreConfig('canvas/configuration/right_safe_margin') : 0),
            $product->getBottomSafeMargin()  ? $product->getBottomSafeMargin() : ($this->getStoreConfig('canvas/configuration/bottom_safe_margin') ? $this->getStoreConfig('canvas/configuration/bottom_safe_margin') : 0),
            $product->getLeftSafeMargin()  ? $product->getLeftSafeMargin() : ($this->getStoreConfig('canvas/configuration/left_safe_margin') ? $this->getStoreConfig('canvas/configuration/left_safe_margin') : 0)
        );
        $this->product['safeMargin'] = $safeMargin;

        $cornerRadius = 0;
        if($product->getCornerRadius()!= null){
            $cornerRadius = $product->getCornerRadius();
        }
        $this->product['cornerRadius'] = $cornerRadius;

        $colorPickerType = 'Full';
        if($product->getElementColorPickerType() != '' && $product->getElementColorPickerType() != null) {
            $colorPickerValue = $product->getElementColorPickerType();
            if($colorPickerValue == 1){
                $colorPickerType = "Full";
            } else if ($colorPickerValue == 2) {
                $colorPickerType = "Printable";
            } else {
                $colorPickerType = "OneColor";
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
        if(count($printableColors) <= 0 && $colorPickerType != "OneColor"){
            $colorPickerType = 'Full';
        }

        $this->product['colors'] = $printableColors;
        $this->product['background_colors'] = $printableBackgroundColors;

        $this->product['colorPickerMode'] = $colorPickerType;
        $this->product['bgcolorPickerMode'] = $bgcolorPickerType;
        $this->product['onlySingleColor'] = ($colorPickerType == "Printable" && $product->getElementColorSettings() == 2) ? 1 : 0;
        $this->product['singleColorHexCode'] = ($colorPickerType == "OneColor" && $product->getSingleColorHexCode() != "") ? $product->getSingleColorHexCode() : null;
        //$this->product['backgroundColor'] = $backgroundColor;
        $this->product['maskImage'] = '';
        $this->product['overlayImage'] = '';
        $this->product['allow_background_image'] = ($product->getAllowBackgroundImage() == 1) ? 1 : 0;
        $this->product['allow_background_color'] = ($product->getAllowBackgroundColor() == 1) ? 1 : 0;
        $this->product['is_double_page'] = $product->getData('is_double_page');
        $this->product['fold_line'] = ($product->getAttributeText('fold_line') == false) ? "None" : $product->getAttributeText('fold_line');
        $this->product['is_vdp'] = ($product->getData('allow_vdp') == 1) ? 1 : 0;
        $this->product['allow_border'] = $product->getAllowBorder() != '' ? $product->getAllowBorder() : 1;
        $this->product['allowed_to_quotemode'] = 0;
        $this->product['allowed_to_ordermode'] = ($product->getData('hide_addtocart') == 1) ? false : true;
        $this->product['allow_text'] = ($product->getData('allow_text') == null) ? 1 : (int) $product->getData('allow_text');
        $this->product['allow_clipart'] = ($product->getData('allow_clipart') == null) ? 1 : (int) $product->getData('allow_clipart');
        $this->product['allow_qr_code'] = ($product->getData('allow_qr_code') == null) ? 1 : (int) $product->getData('allow_qr_code');
        $this->product['allow_image_upload'] = ($product->getData('allow_image_upload') == null) ? 1 : (int) $product->getData('allow_image_upload');
        $this->product['allow_add_page'] = ($product->getData('allow_add_page') == null) ? 0 : (int) $product->getData('allow_add_page');
        $this->product['is_photobook'] = ($product->getData('is_photobook') == null) ? 0 : (int) $product->getData('is_photobook');
        $this->product['cover_extra_width'] = $product->getData('cover_extra_width');
        $this->product['cover_extra_height'] = $product->getData('cover_extra_height');
        $this->product['show_price'] = ($product->getData('hide_price') == 1) ? false : true;

        $mapImage = '';
        $model3d = '';
        $this->product['is_3d'] = ($product->getData('is_3d') == 1 ) ? 1 : 0;


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

        $bgcolorText = "White(#FFFFFF)";
        $bgString = trim($bgcolorText);
        preg_match_all('/\(([^\)]*)\)/', $bgString, $aMatches);
        $color_name = explode('(#',$bgString);
        $this->product['bgcolor'] = $aMatches[1][0];
        $this->product['BgId'] = '';

        $this->product['selectedOptions'] = [];
        $this->product['qty'] = 1;

        if($product->getEnableCustomHeightWidth() == 1 && $product->getPricingLimit() != null){
            $this->product['size_limits'] = $this->pricecalculatorHelper->getMinMaxForCustomSize($product);
        }

        /*$this->getRequest()->getParam('id');
        if(isset($request['design_id']) && $request['design_id'] != ''){
            $this->product['design'] = $this->getCustomerDesign($request['design_id']);
        }*/
        //$this->product['productTemplateCount'] = $this->templateHelper->getProductRelatedAllTemplates(array("pid" => $product->getEntityId()));
        $this->product['productLayoutCount'] = $this->templateHelper->getProductRelatedAllLayouts(array("pid" => $product->getEntityId()));
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
                $customoptionsDesigntoolTypeLabel = $_option->getDesigntoolType();

                $dependentJs = '';
                $values = $_option->getValues();
                $valuesArray = array();
                $count = 1;
                $default = false;
                $temp = false;

                if ($_option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_FIELD && $_option->getType() != \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) {

                    foreach ($values as $_value) {

                        if ($customoptionsDesigntoolTypeLabel == "BACKGROUND COLORS") {

                            if (isset($_value['designtool_title']) && $_value['designtool_title'] != '') {
                                $backgroundColor[] = array('colorCode' => $_value['designtool_title']);
                            } else {
                                $bgcolor = $_value['title'];
                                $bgString = trim($bgcolor);
                                preg_match_all('/\(([^\)]*)\)/', $bgString, $aMatches);
                                $color_name = explode('(', $bgString);
                                // $backgroundColor[$cnt]['name'] = $color_name[0];
                                if (empty($aMatches[1][0])) {
                                    $colorCode = $color_name[0];
                                } else {
                                    $colorCode = $aMatches[1][0];
                                }
                                // $backgroundColor[$cnt]['colorCode'] = $colorCode;
                                $backgroundColor[] = array('name' => $color_name[0], 'colorCode' => $_value['designtool_title']);
                            }
                        }
                        if ($_value->getPriceType() == 'percent') {
                            $option_price = (($price * $_value->getPrice()) / 100);
                        } else {
                            $option_price = $_value->getPrice();
                        }


                        if ($customoptionsDesigntoolTypeLabel == 'sides') {
                            $sideId = $_value['option_id'];
                            $sideOptionId = $_value['option_type_id'];
                        }

                        $valuesArray[] = array(
                            "option_type_id" => $_value['option_type_id'],
                            "mageworx_option_type_id" => $_value['mageworx_option_type_id'],
                            "option_id" => $_value['option_id'],
                            "sku" => $_value['sku'],
                            "default_title" => $_value['title'],
                            "designtool_title" => isset($_value['designtool_title']) && $_value['designtool_title'] != '' ? $_value['designtool_title'] : $_value['title'],
                            //"priceModifier" => Mage::helper('core')->currency($option_price,true,false),
                            "priceModifier" => '',
                            "sort_order" => $_value['sort_order'],
                            "mask_image" => '',
                            "overlay_image" => '',
                            "count" => $count,
                            // "disabled" => ($enabledDependent && $_option->getIsDependent()) ? 'disabled="disabled"' : '',
                            // "default" => $_value['default'] == 1 ? true : false
                            "default" => $default
                        );
                        /*$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
                        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
                        $connection = $resource->getConnection();
                        $tableName = $resource->getTableName('mageworx_option_dependency');


                        $select = $connection->select()->from(
                            $tableName,
                            'child_option_type_id'
                        )->where(
                            'parent_option_id = ?',
                            $_option->getMageworxOptionId()
                        )->where(
                            'parent_option_type_id = ?',
                            $_value->getMageworxOptionTypeId()
                        );

                        $idList = $connection->fetchCol($select);
                        $dependentIds = '';
                        $newIdList = [];
                        if (count($idList)) {
                            //$dependentIds = implode("', '", $idList);
                            $dependentIds = "'" . implode("', '", $idList) . "'";
                            $dependentJs .= 'dependentDefault["select_' . $_option->getId() . '"] = ' . $_value->getOptionTypeId() . ';';
                            //$dependentJs .= 'dependentData['.$_value->getOptionTypeId().'] = ["'.$dependentIds.'"]; ';
                            $dependentJs .= 'dependentData[' . $_value->getOptionTypeId() . '] = [' . $dependentIds . ']; ';

                        } else {

                        }
                        $select = $connection->select()->from(
                            $tableName,
                            'parent_option_type_id'
                        )->where(
                            'child_option_id = ?',
                            $_option->getMageworxOptionId()
                        )->where(
                            'child_option_type_id = ?',
                            $_value->getMageworxOptionTypeId()
                        );
                        $childIdList = $connection->fetchCol($select);
                        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_GROUP_SELECT || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_DROP_DOWN) {

                            if (count($childIdList)) {
                                $dependentJs .= 'inGroupIdData["' . $_value->getMageworxOptionTypeId() . '"] = {"disabled":true, "select_' . $_option->getId() . '":' . $_value->getOptionTypeId() . '}; ';
                            } else {
                                //$dependentJs .= 'inGroupIdData["'.$_value->getMageworxOptionTypeId().'"] = {"disabled":false, "select_'.$_option->getId().'":'.$_value->getOptionTypeId().'}; ';
                            }
                        }
                        if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_RADIO || $_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_CHECKBOX) {
                            if (count($childIdList)) {
                                $dependentJs .= 'inGroupIdData["' . $_value->getMageworxOptionTypeId() . '"] = {"disabled":true, "options_' . $_option->getId() . '_' . $count . '":1}; ';
                            } else {
                                $dependentJs .= 'inGroupIdData["' . $_value->getMageworxOptionTypeId() . '"] = {"disabled":false, "options_' . $_option->getId() . '_' . $count . '":1}; ';
                            }

                        }
                        $count++;*/

                    }
                }

                if ($_option->getType() == \Magento\Catalog\Model\Product\Option::OPTION_TYPE_AREA) 
                {
                    $customoptionsDesigntoolTypeLabel = $_option->getData('title'); 
                }
                
                $option = array(
                    "id" => $_option->getId(),
                    "type" => $_option->getType(),
                    "title" => $_option->getTitle(),
                    "mageworx_option_id" => $_option->getData('mageworx_option_id'),
                    "label" => $customoptionsDesigntoolTypeLabel,
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
            if ($customoptionsDesigntoolTypeLabel == 'sizes') {
                $this->product['option_sizes_dt'] = $option;
            }
        }

        $this->product['backgroundColor'] = $backgroundColor;


        $qtyData = array(
            "id" => 'quantityBox',
            "type" => 'text',
            "title" => __('Quantity'),
            "label" => 'QuantityBox',
            "is_require" => '',
            "sort_order" => '',
            "value" => 1,
        );
        array_push($options, $qtyData);
        $data['options'] = $options;
        $this->product['options'] = $options;
        $this->product['dependentData'] = $this->getDependentData();
        return $this->product;
    }

    public function getTemplateDesign($templateId)
    {
        $designDir = $this->_outputHelper->getTemplateDesignsDir();
        $templateFactory = $this->_templateFactory->create();
        if($this->storeId){
            $storeId = $this->storeId;
        } else {
            $storeId = $this->getStoreId();
        }

        $templateFactory->setStoreId($storeId);
        $templateFactory->load($templateId);

        $design = [];
        $design['design_id'] = '';
        $design['design_name'] = '';
        $design['design_description'] = htmlspecialchars($templateFactory->getDescription());
        //$options = $this->jsonHelper->jsonDecode($templateFactory->getOptions());

        if($templateFactory->getSvg() && $templateFactory->getSvg() != ''){
            $svgs = [];
            $svgs = explode(',', $templateFactory->getSvg());
            foreach ($svgs as $svg) {
                if(file_exists($designDir.$svg)){
                    $design['svg'][] = file_get_contents($designDir.$svg);
                }
            }
        }
        return $design;
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
                $design['qty'] = $itemOptions['qty'];
            }
            if(isset($itemOptions) && !empty($itemOptions['super_attribute'])){
                foreach ($itemOptions['super_attribute'] as $index => $value) {
                    if($this->colorID != '' && $this->colorID == $index){
                        $design['colorOptionId'] = $value;
                        $design['qty'] = $itemOptions['qty'];
                    }
                }
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
        }
        return $design;
    }

    public function isQuickEditEnable($_product)
    {
        $productId = $_product->getId();
        $store = $_product->getStoreId();
        $canvasPersonalizeOption = $this->getCanvasOptions($productId, $store, $_product);
        
        if (($canvasPersonalizeOption == 2
                || $canvasPersonalizeOption == 3)
            && $_product->getTemplateId() != '' && $_product->getIsCustomizable() && $_product->getEnableCustomHeightWidth() != 1) {
            return true;
        }
        return false;
    }
    public function getCanvasOptions($productId, $store, $_product)
    {
       return $_product->getResource()->getAttributeRawValue($productId, 'canvas_personalize_option', $store);
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