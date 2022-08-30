<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Base\Helper;

use Magento\Framework\App\Action\Action;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Json\DecoderInterface;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Math\Division as MathDivision;
use Magento\Store\Model\ScopeInterface;
use Designnbuy\Customer\Helper\Data as CustomerHelper;
/**
 * Designnbuy Clipart Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**#@+
     * Product Base Unit values
     */
    const UNIT_INCHES = 1;
    const UNIT_MILIMETER = 2;
    const UNIT_CENTIMETER = 3;
    const UNIT_PIXELS = 4;
    const DIRECTORY_SEPARATOR = '/';

    const DESIGNNBUY_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR;

    const CUSTOMER_IMAGE_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'uploadedImage'. SELF::DIRECTORY_SEPARATOR;

    const ORDER_ATTACHMENT_IMAGE_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'orderattachment'. SELF::DIRECTORY_SEPARATOR;

    const ADMIN_IMAGE_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'adminimages'. SELF::DIRECTORY_SEPARATOR;

    const TEMPLATE_SVG_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'template'. SELF::DIRECTORY_SEPARATOR;

    const DESIGNIDEA_SVG_PATH = 'designnbuy'. SELF::DIRECTORY_SEPARATOR .'designidea'. SELF::DIRECTORY_SEPARATOR;

    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $setup;
    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DecoderInterface
     */
    protected $jsonDecoder;
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Processor
     */
    protected $itemProcessor;

    /**
     * @var StockRegistryProviderInterface
     */
    protected $stockRegistryProvider;

    /**
     * @var StockConfigurationInterface
     */
    protected $stockConfiguration;

    /**
     * @var StockStateProviderInterface
     */
    protected $stockStateProvider;

    /**
     * @var \Designnbuy\PrintingMethod\Helper\Data
     */
    private $printingMethodHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    private $priceHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Designnbuy\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @var \Designnbuy\CustomerPhotoAlbum\Helper\Data
     */
    protected $_customerImageHelper;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var string
     */
    protected $rootDirBasePath;

    /**
     * @var \Designnbuy\Merchandise\Helper\Data
     */
    protected $merchandiseHelper;

    /**
     * Init
     * @param MathDivision $mathDivision
     * @param FormatInterface $localeFormat
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param EavSetupFactory $eavSetupFactory
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     * @param ProductRepositoryInterface $productRepository
     * @param StockStateProviderInterface $stockStateProvider
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Quote\Item\Processor $itemProcessor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        MathDivision $mathDivision,
        FormatInterface $localeFormat,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $setup,
        \Magento\Framework\Filesystem $filesystem,
        EncoderInterface $jsonEncoder,
        DecoderInterface $jsonDecoder,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\Quote\Item\Processor $itemProcessor,
        \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Model\Spi\StockRegistryProviderInterface $stockRegistryProvider,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Designnbuy\PrintingMethod\Helper\Data $printingMethodHelper,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\UrlInterface $urlBuilder,
        CustomerHelper $customerHelper,
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $customerImageHelper,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Designnbuy\Merchandise\Helper\Data $merchandiseHelper,
        $rootDirBasePath = DirectoryList::MEDIA
    ){
        parent::__construct($context);
        $this->mathDivision = $mathDivision;
        $this->localeFormat = $localeFormat;
        $this->_storeManager = $storeManager;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->setup = $setup;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->jsonEncoder = $jsonEncoder;
        $this->jsonDecoder = $jsonDecoder;
        $this->productRepository = $productRepository;
        $this->itemProcessor = $itemProcessor;
        $this->objectFactory = $objectFactory;
        $this->stockStateProvider = $stockStateProvider;
        $this->stockRegistryProvider = $stockRegistryProvider;
        $this->stockConfiguration = $stockConfiguration;
        $this->printingMethodHelper = $printingMethodHelper;
        $this->priceHelper = $priceHelper;
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_urlBuilder = $urlBuilder;
        $this->_customerHelper = $customerHelper;
        $this->_customerImageHelper = $customerImageHelper;
        $this->_fileFactory = $fileFactory;
        $this->rootDirBasePath = $rootDirBasePath;
        $this->merchandiseHelper = $merchandiseHelper;
        $this->_moduleManager = $context->getModuleManager();

    }

    public function getUnit($value)
    {
        switch ($value) {
            case self::UNIT_INCHES:
                return 'in';
            case self::UNIT_MILIMETER:
                return 'mm';
            case self::UNIT_CENTIMETER:
                return 'cm';
            case self::UNIT_PIXELS:
                return 'px';
            default:
                return 'in';
        }
    }

    public function getCustomProductAttributeSetId()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSet($entityTypeId, 'CustomProduct', 'attribute_set_id');
        return $attributeSetId;
    }

    public function getCustomCanvasAttributeSetId()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getAttributeSet($entityTypeId, 'CustomPrint', 'attribute_set_id');
        return $attributeSetId;
    }

    public function isCanvasProduct($product)
    {
        if($product->getAttributeSetId() == $this->getCustomCanvasAttributeSetId()){
            return true;
        }
        return false;
    }
    public function isMerchandiseProduct($product)
    {
        if($product->getAttributeSetId() == $this->getCustomProductAttributeSetId()){
            return true;
        }
        return false;
    }

    public function getMediaUrl()
    {
        return $this->_urlBuilder->getBaseUrl(
            ['_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA]
        );
    }

    public function getDesignNBuyDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::DESIGNNBUY_PATH);
    }

    public function getCustomerImageDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::CUSTOMER_IMAGE_PATH);
    }

    public function getCustomerImageUrl()
    {
        $customerImagePath = str_replace('\\','/',self::CUSTOMER_IMAGE_PATH);
        return $this->getMediaUrl().$customerImagePath;
    }

    public function getAdminImageDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::ADMIN_IMAGE_PATH);
    }

    public function getAdminImageUrl()
    {
        $adminImagePath = str_replace('\\','/',self::ADMIN_IMAGE_PATH);
        return $this->getMediaUrl().$adminImagePath;
    }

    public function getProductPrice($data)
    {
        $pricingData = [];

        $result = [];
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL;

        if(isset($data) && !empty($data['pricingData'])){
            $pricingData = $data['pricingData'];
            $params = $this->jsonDecoder->decode($pricingData);
            if(isset($data['options']) && !empty($data['options'])){
                $params['options'] = $data['options'];
            }
            if (isset($params['productId'])) {
                $productId = $params['productId'];
                $product = $this->_initProduct($productId);
            }
        } else{
            $params = $data;
            if (isset($params['product'])) {
                $productId = $params['product'];
                $product = $this->_initProduct($productId);
            }
        }

        if (!$product) {
            return;
        }

        $totalFinalPrice = 0;
        $finalPrice = 0;

        $requestInfo = [];
        if(isset($params['colorId'])){
            $colorAttributeId = $this->merchandiseHelper->getColorAttributeId();
            $requestInfo['super_attribute'][$colorAttributeId] = $params['colorId'];
        }


        $printingPrice = 0;
        $nameNumberPrice = 0;
        $nameTotalPrice = 0;
        $nameTotal = 0;
        $numberTotalPrice = 0;
        $numberTotal = 0;
        $sizesPrice = [];
        $finalPriceTotal = [];

        if(isset($params['sizesData']) && !empty($params['sizesData'])){
            $printingPrices = array();
            $sizeData = $params['sizesData'];
            $totalQty  = array_sum(array_column($params['sizesData'],'quantity'));
            foreach ($sizeData as $size) {
                $qty = $size['quantity'];
                $sizeAttributeId = $this->merchandiseHelper->getSizeAttributeId();
                $requestInfo['super_attribute'][$sizeAttributeId] = $size['id'];
                $requestInfo['qty'] = $qty;
                if(isset($params['options']) && !empty($params['options'])){
                    $requestInfo['options'] = $params['options'];
                }
                $requestInfo['qty'] = $qty;
                $request = $this->_getProductRequest($requestInfo);
                $product->setFinalPrice(null);
                $cartCandidates = $product->getTypeInstance()->prepareForCartAdvanced($request, $product, $processMode);
                $finalPrice = round($product->getFinalPrice($qty, $product),0);
                $finalPriceTotal[$size['id']] = $finalPrice * $qty;
                $sizesPrice[$size['id']] = $this->getFormattedPrice($finalPrice * $qty);
                if (isset($params['printingMethod'])) {
                    if(isset($size['totalname']) || !empty($size['totalnumber'])){
                        if(isset($size['totalname'])){
                            $nameTotal = $size['totalname'];
                            $params['printingMethod']['nameNumber']['totalname'] = $nameTotal;
                        }
                        if(isset($size['totalnumber'])){
                            $numberTotal = $size['totalnumber'];
                            $params['printingMethod']['nameNumber']['totalnumber'] = $numberTotal;
                        }
                    }
                }
            }
            if (isset($params['printingMethod'])) {
                $printingPrices = $this->printingMethodHelper->calculatePrintingMethodPrice($params['printingMethod'], $totalQty);
                $nameTotalPrice = $printingPrices['nameTotalPrice'];
                $numberTotalPrice = $printingPrices['numberTotalPrice'];

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotal = $params['nameNumber']['totalname'];
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotal = $params['nameNumber']['totalnumber'];
                    }
                }

                $nameNumberPrice = ($nameTotal * $nameTotalPrice) + ($numberTotal * $numberTotalPrice);

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotalPrice = $nameTotalPrice * $nameTotal;
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotalPrice = $numberTotalPrice * $numberTotal;
                    }
                }
                if(!empty($finalPriceTotal)){
                    $finalPrice = array_sum($finalPriceTotal);
                }
                
                $printingPrice = $printingPrice + ($printingPrices['printingPrice'] * $totalQty);
                $totalFinalPrice = $totalFinalPrice + ($finalPrice);
            }
        } else if(isset($params['colorId']) && !empty($params['colorId'])){
            $printingPrice = 0;
            $squareAreaPrice = 0;
            $qty = $params['qty'];
            $requestInfo['qty'] = $qty;
            if (isset($params['options']) && !empty($params['options'])) {
                $requestInfo['options'] = $params['options'];
            }

            $request = $this->_getProductRequest($requestInfo);
            $cartCandidates = $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product, $processMode);
            $finalPrice = round($product->getFinalPrice($qty, $product),2);

            $totalFinalPrice = $totalFinalPrice + ($finalPrice * $qty);
            $printingPrices = array();
            if (isset($params['printingMethod']) && !empty($params['printingMethod'])) {
                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    $params['printingMethod']['nameNumber'] = $params['nameNumber'];
                }
                $printingPrices = $this->printingMethodHelper->calculatePrintingMethodPrice($params['printingMethod'], $qty);
                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    $params['printingMethod']['nameNumber'] = $params['nameNumber'];
                    if(isset($params['nameNumber']['totalname']) || !empty($params['nameNumber']['totalnumber'])){
                        if(isset($params['nameNumber']['totalname'])){
                            $nameTotal = $params['nameNumber']['totalname'];
                        }
                        if(isset($params['nameNumber']['totalnumber'])){
                            $numberTotal = $params['nameNumber']['totalnumber'];
                        }
                    }
                }
                $nameTotalPrice = $printingPrices['nameTotalPrice'];
                $numberTotalPrice = $printingPrices['numberTotalPrice'];

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotal = $params['nameNumber']['totalname'];
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotal = $params['nameNumber']['totalnumber'];
                    }
                }

                $nameNumberPrice = ($nameTotal * $nameTotalPrice) + ($numberTotal * $numberTotalPrice);

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotalPrice = $nameTotalPrice * $nameTotal;
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotalPrice = $numberTotalPrice * $numberTotal;
                    }
                }

                $printingPrice = $printingPrice + ($printingPrices['printingPrice'] * $qty);
            }          
        } else {
            $printingPrice = 0;
            $optionCalculation = 0;
            $qty = $params['qty'];
            $requestInfo['qty'] = $qty;
            if (isset($params['options']) && !empty($params['options'])) {
                $requestInfo['options'] = $params['options'];
            }

            $request = $this->_getProductRequest($requestInfo);
            $cartCandidates = $product->getTypeInstance(true)->prepareForCartAdvanced($request, $product, $processMode);
            $finalPrice = round($product->getFinalPrice($qty, $product),2);
            
            /*For Custom Height Width Price Start*/
            if ($this->_moduleManager->isEnabled('Designnbuy_Pricecalculator')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $priceCalculatorHelper = $objectManager->get('\Designnbuy\Pricecalculator\Helper\Data');
                if(isset($request['options']) && !empty($request['options'])){
                    if($product->getEnableCustomHeightWidth() == 1 && $product->getPricingLimit() != null) {
                        $optionCalculation = $priceCalculatorHelper->calculateCSVPrice($product, $request['options']);
                        //echo $optionCalculation; exit;
                        if($optionCalculation){
                            $basePrice = $product->getPrice();
                            $product->setPrice($optionCalculation);
                            $optionCalculationdt = $product->getFinalPrice($qty,$product);
                            //$item->getProduct()->setPrice($basePrice);
                            $product->setPrice($basePrice);
                            $finalPriceBkp = $product->getFinalPrice($qty,$product);
                        } else {
                            $finalPriceBkp = $product->getFinalPrice($qty,$product);
                        }

                        if($product->getAbsolutePrice()){
                            $finalPriceBkp = 0;
                            if($optionCalculation){
                                //echo (int)$optionCalculation ."+". (int)$optionCalculationdt; exit;
                                if((int)$optionCalculation != (int)$optionCalculationdt){
                                    $optionCalculation = $optionCalculation + $optionCalculationdt;
                                } else {
                                    $optionCalculation = $optionCalculationdt;    
                                }
                            }
                        } else {
                            if($optionCalculation){
                                $optionCalculation = $optionCalculationdt;
                                //echo $finalPriceBkp ."+". $optionCalculationdt; exit;
                            }
                        }

                        //echo $finalPriceBkp ."+". $optionCalculation; exit;
                        $finalPrice = $finalPriceBkp + $optionCalculation;

                    }
                }
            }
            /*For Custom Height Width Price End*/
            
            $totalFinalPrice = $totalFinalPrice + ($finalPrice * $qty);
            
            $printingPrices = array();
            if (isset($params['printingMethod']) && !empty($params['printingMethod'])) {
                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    $params['printingMethod']['nameNumber'] = $params['nameNumber'];
                }
                $printingPrices = $this->printingMethodHelper->calculatePrintingMethodPrice($params['printingMethod'], $qty);
                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    $params['printingMethod']['nameNumber'] = $params['nameNumber'];
                    if(isset($params['nameNumber']['totalname']) || !empty($params['nameNumber']['totalnumber'])){
                        if(isset($params['nameNumber']['totalname'])){
                            $nameTotal = $params['nameNumber']['totalname'];
                        }
                        if(isset($params['nameNumber']['totalnumber'])){
                            $numberTotal = $params['nameNumber']['totalnumber'];
                        }
                    }
                }
                $nameTotalPrice = $printingPrices['nameTotalPrice'];
                $numberTotalPrice = $printingPrices['numberTotalPrice'];

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotal = $params['nameNumber']['totalname'];
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotal = $params['nameNumber']['totalnumber'];
                    }
                }

                $nameNumberPrice = ($nameTotal * $nameTotalPrice) + ($numberTotal * $numberTotalPrice);

                if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                    if(isset($params['nameNumber']['totalname']) && $params['nameNumber']['totalname'] != ""){
                        $nameTotalPrice = $nameTotalPrice * $nameTotal;
                    }
                    if(isset($params['nameNumber']['totalnumber']) && $params['nameNumber']['totalnumber'] != ""){
                        $numberTotalPrice = $numberTotalPrice * $numberTotal;
                    }
                }

                $printingPrice = $printingPrice + ($printingPrices['printingPrice'] * $qty);
            }
        }

        $formattedFinalPrice = $this->getFormattedPrice($finalPrice);
        if (isset($params['printingMethod']) && !empty($params['printingMethod']) && $params['printingMethod']['printingMethodId'] != 0) {
            $result['printingPrice'] = $this->getFormattedPrice($printingPrice);
            $artWorkSetupPrice = 0;
            if($printingPrices && !empty($printingPrices['artWorkSetupPrice'])){
                $artWorkSetupPrice = $printingPrices['artWorkSetupPrice'];
            }
            $result['artWorkSetupPrice'] = $this->getFormattedPrice($artWorkSetupPrice);
            $result['printingPrice'] = $this->getFormattedPrice($printingPrice);
            $result['good'] = $this->getFormattedPrice($totalFinalPrice + $printingPrice + $artWorkSetupPrice + $nameNumberPrice);
        } else {
            if(isset($params['nameNumber']) && !empty($params['nameNumber'])){
                $namePrice = $this->getNamePrice();
                $nameNumber = (array) $params['nameNumber'];

                if(isset($nameNumber['totalname']) && !empty($nameNumber['totalname'])){
                    $totalName = $nameNumber['totalname'];
                    $namePrice = (float)$this->getNamePrice();
                    $nameTotalPrice = $totalName * $namePrice;
                }

                if(isset($nameNumber['totalnumber']) && !empty($nameNumber['totalnumber'])){
                    $totalNumber = $nameNumber['totalnumber'];
                    $numberPrice = (float)$this->getNumberPrice();
                    $numberTotalPrice = $totalNumber * $numberPrice;
                }
                $nameNumberPrice = $nameTotalPrice + $numberTotalPrice;
                $totalFinalPrice = $totalFinalPrice + $nameNumberPrice;
            }
            $result['artWorkSetupPrice'] = $this->getFormattedPrice(0);
            $result['printingPrice'] = $this->getFormattedPrice(0);
            $result['good'] = $this->getFormattedPrice($totalFinalPrice);
        }

        $totalFinalPrice = $this->getFormattedPrice($totalFinalPrice);
        $result['message'] = '';
        $result['baseTotal'] = $formattedFinalPrice;
        $result['basePrice'] = $totalFinalPrice;
        $result['colorSizeTotalPrice'] = '';
        $result['nameTotalPrice'] = $this->getFormattedPrice($nameTotalPrice);
        $result['numberTotalPrice'] = $this->getFormattedPrice($numberTotalPrice);
        $result['nameNumberPrice'] = $this->getFormattedPrice($nameNumberPrice);
        $result['unitPrice'] = $this->getFormattedPrice($finalPrice);
        $result['totalPrice'] = $totalFinalPrice;
        $result['sizesPrice'] = $sizesPrice;
        return $result;
    }

    protected function getFormattedPrice($price)
    {
        return $this->priceHelper->currency($price, true, false);
    }

    /**
     * @param int $productId
     * @param float $itemQty
     * @param float $qtyToCheck
     * @param float $origQty
     * @param int $scopeId
     * @return int
     */
    public function checkQuoteItemQty($productId, $qty, $summaryQty, $origQty = 0, $scopeId = null)
    {
        // if ($scopeId === null) {
        $scopeId = $this->stockConfiguration->getDefaultScopeId();
        // }
        $stockItem = $this->stockRegistryProvider->getStockItem($productId, $scopeId);
        return $this->stockStateProvider->checkQuoteItemQty($stockItem, 1, 1, $origQty);
    }

    /**
     * Retrieve quote item by product id
     *
     * @param   \Magento\Catalog\Model\Product $product
     * @return  \Magento\Quote\Model\Quote\Item|bool
     */
    public function getItemByProduct($product)
    {
        return false;
    }

    /**
     * Initialize product instance from request data
     *
     * @return \Magento\Catalog\Model\Product|false
     */
    protected function _initProduct($productId)
    {
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                return false;
            }
        }
        return false;
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   \Magento\Framework\DataObject|int|array $requestInfo
     * @return  \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
        } elseif (is_array($requestInfo)) {
            $request = new \Magento\Framework\DataObject($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }

        return $request;
    }


    public function getVariables()
    {

    }


    public function getMediaPath()
    {
        return $this->_mediaDirectory->getAbsolutePath();
    }


    public function getTemplateSVGDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::TEMPLATE_SVG_PATH);
    }

    public function getDesignIdeaSVGDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::DESIGNIDEA_SVG_PATH);
    }

    public function getCustomerImages()
    {

    }

    public function getFacebookAPIKey()
    {
        return $this->_getConfigValue('base/social/facebook');
    }

    public function getFlickrAPIKey()
    {
        return $this->_getConfigValue('base/social/flickr');
    }

    public function getInstagramAPIKey()
    {
        return $this->_getConfigValue('base/social/instagram');
    }

    public function getMerchandiseImageDPI()
    {
        return $this->_getConfigValue('merchandise/configuration/image_dpi');
    }

    public function getCanvasImageDPI()
    {
        return $this->_getConfigValue('canvas/configuration/image_dpi');
    }

    public function getMerchandiseOutputFormat()
    {
        return $this->_getConfigValue('merchandise/configuration/output_format');
    }

    public function getCanvasOutputFormat()
    {
        return $this->_getConfigValue('canvas/configuration/output_format');
    }

    public function getCanvasOrderInformationFormat()
    {
        return $this->_getConfigValue('canvas/configuration/order_information');
    }

    public function getMerchandiseOrderInformationFormat()
    {
        return $this->_getConfigValue('merchandise/configuration/order_information');
    }

    public function getBaseUnit()
    {
        return $this->_getConfigValue('canvas/configuration/base_unit');
    }
    public function getmerchandiseBaseUnit()
    {
        return $this->_getConfigValue('merchandise/configuration/base_unit');
    }
    public function getPDFOutputType()
    {
        return $this->_getConfigValue('base/output/type');
    }

    public function getWaterMarkImage()
    {
        return $this->_getConfigValue('base/watermark/image');
    }

    public function getWaterMarkText()
    {
        return $this->_getConfigValue('base/watermark/text');
    }

    public function getWaterMarkType()
    {
        return $this->_getConfigValue('base/watermark/type');
    }

    public function isWatermarkEnabled()
    {
        return $this->_getConfigValue('base/watermark/status');
    }

    public function getVDPOutputArea()
    {
        return $this->_getConfigValue('canvas/configuration/vdp');
    }

    public function getPrimaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/primary');
    }

    public function getSecondaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/secondary');
    }

    public function getTertiaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/tertiary');
    }

    public function getQuaternaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/quaternary');
    }

    public function getQuinaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/quinary');
    }

    public function getSenaryColor()
    {
        return $this->_getConfigValue('base/studio_color_scheme/senary');
    }

    public function getNamePrice()
    {
        return $this->_getConfigValue('merchandise/configuration/name_price');
    }

    public function getNumberPrice()
    {
        return $this->_getConfigValue('merchandise/configuration/number_price');
    }
    public function getMerchandiseHelpPage()
    {
        return $this->_getConfigValue('merchandise/configuration/tool_help_page');
    }
    public function getCanvasHelpPage()
    {
        return $this->_getConfigValue('canvas/configuration/tool_help_page');
    }
    public function getPersonolizeButton()
    {
        return $this->_getConfigValue('base/studio_button_label/personolize_button');
    }
    public function getGcaptchaPublickey()
    {
        return $this->_getConfigValue('msp_securitysuite_recaptcha/general/public_key');
    }
    public function getGcaptchaFrontendEnable()
    {
        return $this->_getConfigValue('msp_securitysuite_recaptcha/frontend/enabled');
    }
    public function getGcaptchaFrontendLogin()
    {
        return $this->_getConfigValue('msp_securitysuite_recaptcha/frontend/enabled_login');
    }
    public function getGcaptchaFrontendRegistration()
    {
        return $this->_getConfigValue('msp_securitysuite_recaptcha/frontend/enabled_create');
    }

    /**
     * Retrieve config value
     * @return string
     */
    protected function _getConfigValue($param)
    {
        return $this->_scopeConfig->getValue(
            $param,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function download($url, $isFront = 1)
    {
        //$url = $this->getRequest()->getParam('url', false);
        $url = urldecode($url);
        $url = str_replace(' ', '+', $url);
        $imageData = explode('.',$url);
        $dotCount = count($imageData);
        $imageName = uniqid().'.'.$imageData[$dotCount-1];
        //$imageName = 'designnbuy/uploadedImage/'.$name;
        $saveto =  $this->getCustomerImageDir();
        if($isFront == 0){
            $saveto = $this->getAdminImageDir();
        }
        $ch = curl_init ($url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $raw=curl_exec($ch);

        curl_close ($ch);
        if(file_exists($saveto . $imageName)){
            unlink($saveto . $imageName);
        }
        //$fp = fopen($saveto);
        $fp = fopen($saveto . $imageName, 'w');
        fwrite($fp, $raw);
        fclose($fp);
        //$id = Mage::helper('design')->saveUserImage($name);
        $id = '';
        $result = array();
        $result['id'] = $id;
        $result['imageName'] = $imageName;
        return $result;
    }

    public function upload($request, $files) {
        if(isset($request["isUpload"]) && $request["isUpload"]==1){
            $response = array();
            // 5 minutes execution time
            @set_time_limit(5 * 60);

            // Uncomment this one to fake upload time
            // usleep(5000);

            // Settings
            //$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
            $targetDir = $this->getCustomerImageDir();
            if($request['isFront'] == 0){
                $targetDir = $this->getAdminImageDir();
            }


            /* if($request['isVdp']==true){
                $targetDir = Mage::getBaseDir('media'). DS .'VDPuploadedImage'. DS;
            } */
            // $targetDir = 'uploads';
            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds


            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
            }

            // Get a file name
            if (isset($request["name"])) {
                $fileName = $request["name"];
            } elseif (!empty($files)) {
                $fileName = $files["file"]["name"];
            } else {
                $fileName = uniqid("file_");
            }
            if($request['isFront']==0){
                $fileName = 'admin_'.$fileName;
            }
            $filePath = $targetDir . SELF::DIRECTORY_SEPARATOR . $fileName;

            // Chunking might be enabled
            $chunk = isset($request["chunk"]) ? intval($request["chunk"]) : 0;
            $chunks = isset($request["chunks"]) ? intval($request["chunks"]) : 0;


            // Remove old temp files
            if ($cleanupTargetDir) {
                if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                    die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
                }

                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . SELF::DIRECTORY_SEPARATOR . $file;

                    // If temp file is current file proceed to the next
                    if ($tmpfilePath == "{$filePath}.part") {
                        continue;
                    }

                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                        @unlink($tmpfilePath);
                    }
                }
                closedir($dir);
            }

            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                $response['jsonrpc'] = 2.0;
                $response['error'] = array('code' => 102, "message" => "Failed to open output stream.");
                $response['id'] = 'id';
                //die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
            }

            if (!empty($files)) {
                if ($files["file"]["error"] || !is_uploaded_file($files["file"]["tmp_name"])) {
                    $response['jsonrpc'] = 2.0;
                    $response['error'] = array('code' => 102, "message" => "Failed to move uploaded file.");
                    $response['id'] = 'id';
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
                }
                // Read binary input stream and append it to temp file
                if (!$in = @fopen($files["file"]["tmp_name"], "rb")) {
                    $response['jsonrpc'] = 2.0;
                    $response['error'] = array('code' => 102, "message" => "Failed to open input stream.");
                    $response['id'] = 'id';
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    $response['jsonrpc'] = 2.0;
                    $response['error'] = array('code' => 102, "message" => "Failed to open input stream.");
                    $response['id'] = 'id';
                    //die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
                }
            }

            /*
             * If response is having an error, return error message.
             */
            if(array_key_exists('error', $response) == false) {
                //return json_encode($response);

                while ($buff = fread($in, 4096)) {
                    fwrite($out, $buff);
                }

                @fclose($out);
                @fclose($in);

                // Check if file has been uploaded
                if (!$chunks || $chunk == $chunks - 1) {
                    // Strip the temp .part suffix off
                    rename("{$filePath}.part", $filePath);
                    $isHD = '';
                    $imageId = '';
                    $id = '';
                    if(isset($request['isHD'])){
                        $isHD = $request['isHD'];
                    }

                    if(isset($request['imageId'])){
                        $imageId = $request['imageId'];
                    }

                    $convertableExtensions = array("ai", "psd", "pdf", "eps");
                    $exploded_fileNname = explode(".",$fileName);
                    $fileInfo = pathinfo($fileName);
                    $fileType = $fileInfo['extension'];
                    $fileType = $exploded_fileNname[1];
                    if(in_array(strtolower($fileType), $convertableExtensions)){
                        $file_dir_path = $targetDir . SELF::DIRECTORY_SEPARATOR .$fileName;
                        if($fileType != "eps" ) {
                            $file_dir_path = $targetDir . SELF::DIRECTORY_SEPARATOR.$fileName."[0]";
                        }
                        $fileName = $fileName.".png";
                        $image_dir_path = $targetDir . SELF::DIRECTORY_SEPARATOR  .$fileName;
                        exec("convert -density 400 $file_dir_path -quality 100 $image_dir_path");
                    }


                    if($request['isFront']==1){
                        $id = $this->_customerHelper->saveUserImage($fileName, $isHD, $imageId);

                        /*if($request["toolType"] == 'producttool'){
                            //$id = Mage::helper('design')->saveUserImage($fileName,$isHD,$imageId);
                        }else{
                            //$id = Mage::helper('web2print')->saveUserImage($fileName,$isHD,$imageId);
                        }*/
                    }else{
                        if(!isset($request['type']) || (isset($request['type'])  && $request['type'] != 'mask' && $request['type'] != 'overlay')){
                            $id = $this->_customerImageHelper->saveAdminImage($fileName, $isHD, $imageId,$request['cur_album_id'],$files);
                        }
                    }


                    $response['jsonrpc'] = 2.0;
                    $response['status'] = 'success';
                    $response['id'] = $id;
                    $response['OK'] = 1;
                    if($request['isFront'] == 0){
                        $response['image_url'] = $this->getAdminImageUrl().$fileName;
                    } else {
                        $response['image_url'] = $this->getCustomerImageUrl().$fileName;
                    }
                    $response['dir'] = $targetDir;
                    $response['file_path'] = $filePath;
                    //if(isset($request['isMobile']) && $request['isMobile'] == 1){
                        $this->correctImageOrientation($targetDir.$fileName);
                    //}
                    /* Vdp zip */
                    if(isset($request['imageId']) && $request['imageId'] == 'uploadZipBtn')
                    {
                        $File = $fileName; //$request["name"];
                        $zipfile = $this->getCustomerImageDir() . $File;
                        $zip = new \ZipArchive();
                        $cur_time = 'VDPimages-' . time();
                        $imagepath = $this->getCustomerImageDir() . $cur_time;
                        $imagepathUrl = $this->getCustomerImageUrl() . $cur_time;
                        $zipList = array();
                        if (!is_dir($imagepath)) {
                            mkdir($imagepath, 0777);
                        }
                        if ($zip->open($zipfile, \ZipArchive::CREATE) == TRUE) {
                            //$zip->extractTo($imagepath);
                            for ($i = 0; $i < $zip->numFiles; $i++) {
                                $info = $zip->statIndex($i);
                                $file = pathinfo($info['name']);
                                if ((strtolower($file['extension']) == "jpg") || (strtolower($file['extension']) == "png") || (strtolower($file['extension']) == "jpeg")) {
                                    $zipList[] = basename($info['name']);
                                    file_put_contents($imagepath . '/' . basename($info['name']), $zip->getFromIndex($i));
                                } else {
                                    $zip->deleteIndex($i);
                                }
                            }
                        }
                        $zip->close();
                        //unlink($zipfile);
                        $response['imagepath'] = $imagepathUrl;
                        $response['Vdpimages'] = $zipList;
                    }
                    /* End vdp */
                }
            }
            return json_encode($response);
        }else{
            if($request['isFront']==1){
                $isHD = '';
                $imageId = '';
                $images = $request['images'];

                foreach($images as $image){
                    $this->_customerHelper->saveUserImage($image, $isHD, $imageId);
                }
            }
        }
    }
    public function correctImageOrientation($filename) {
        if (function_exists('exif_read_data')) {
            $destination_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if(in_array($destination_extension, ["jpg","jpeg"]))
            {
                try {
                    $exif = @exif_read_data($filename);
                    if($exif && isset($exif['Orientation'])) {
                        $orientation = $exif['Orientation'];
                        
                        if($orientation != 0){
                        $img = imagecreatefromjpeg($filename);
                        $deg = 0;
                        switch ($orientation) {
                            case 1://0 degrees: the correct orientation, no adjustment is required.
                            $deg = 0;
                            break;
                            case 2://0 degrees, mirrored: image has been flipped back-to-front.
                            $deg = 0;
                            break;
                            case 3://180 degrees: image is upside down.
                            $deg = 180;
                            break;
                            case 4://180 degrees, mirrored: image is upside down and flipped back-to-front.
                            $deg = 180;
                            break;
                            case 5://90 degrees: mirrored: image is on its side and flipped back-to-front.
                            $deg = 0;
                            break;
                            case 6://90 degrees, image is on its far side.
                            $deg = 90;
                            break;
                            case 7://270 degrees: image is on its far side and flipped back-to-front.
                            $deg = 0;
                            break;
                            case 8://270 degrees, 
                            $deg = 270;
                            break;
                        }
                        if($deg){
                            $img = imagerotate($img, $deg, 0);        
                        }
                        // then rewrite the rotated image back to the disk as $filename 
                        imagejpeg($img, $filename, 95);
                        } // if there is some rotation necessary
                        
                    }
                } catch (\Exception $e) {
                    
                }
            } // if have the exif orientation info
        } // if function exists added by GvB     
    }
    public function removeWhiteFromImage($params)
    {
        $imagePath = $this->getCustomerImageDir();
        $imagePathUrl = $this->getCustomerImageUrl();

        if (isset($params) && array_key_exists('isQuickEdit', $params) && $params['isQuickEdit'] == 1) {
            $imagePath = $this->getOrderAttachementImageDir();
            $imagePathUrl = $this->getOrderAttachementImageUrl();
        }
        if (isset($params) && array_key_exists('imageUrl', $params)) {
            $imageUrl = $params['imageUrl'];
            $pathInfo = pathinfo($imageUrl);

            if(isset($params['is_admin'])){
                $imagePath = $this->getAdminImageDir();
                $imagePathUrl = $this->getAdminImageUrl();
            }

            $imageName = $pathInfo['basename'];
            if(!file_exists($imagePath.$imageName)){
                return $response['success'] = false;
            }
            $image = $imagePath.$imageName;
            $dir = $imagePath;
            $outputImageName = $pathInfo['filename'].'_'.time() . '.png';
            $outputImage = $dir . $outputImageName;

            $background = '#ffffff';
            $command = "convert $image png:- | convert png:- -fuzz 10% -transparent '$background'  $outputImage";
            shell_exec($command);
            $response['url'] = $imagePathUrl.$outputImageName;
            $response['success'] = true;
        } else {
            $response['success'] = false;
        }
        return $response;

    }
    public function removeWhiteFromEdges($params)
    {
        $imagePath = $this->getCustomerImageDir();
        $imagePathUrl = $this->getCustomerImageUrl();

        if (isset($params) && array_key_exists('isQuickEdit', $params) && $params['isQuickEdit'] == 1) {
            $imagePath = $this->getOrderAttachementImageDir();
            $imagePathUrl = $this->getOrderAttachementImageUrl();
        }

        $outputpng = time().'.png';
        if (file_exists($imagePath.$outputpng)) {
            $index = 1;
            $baseName = time() . '.png';
            while(file_exists($imagePath . $baseName)) {
                $baseName = time(). '_' . $index . '.png' ;
                $index ++;
            }
            $outputpng = $baseName;
        } 
        if (isset($params) && array_key_exists('imageUrl', $params)) {
            $imageUrl = $params['imageUrl'];
            $pathInfo = pathinfo($imageUrl);
            
            $imageName = $pathInfo['basename'];

            if(!file_exists($imagePath.$imageName)){
                return $response['success'] = false;
            }

            $image = $imagePath.$imageName;
            $dir = $imagePath;
            $outputImage = $dir . $outputpng;
            
            $im = new \Imagick($image);
            list($w, $h) = array_values($im->getImageGeometry());
            $backgroundColor  = $im->getImageBackgroundColor();
            $transparentColor = "rgb(255, 0, 255)";
            // image top edge
            for($i=0;$i<$w;$i++){
                $im->floodfillPaintImage($transparentColor, 2500, $backgroundColor, $i,0,false);
            }
            // image left edge
            for($i=0;$i<$h;$i++){
                $im->floodfillPaintImage($transparentColor, 2500, $backgroundColor, 0,$i,false);
            } 
            // image bottom edge
            for($i=0;$i<$w-1;$i++){
                $im->floodfillPaintImage($transparentColor, 2500, $backgroundColor,$i,$h-1,false);
            }
            // image right edge
            for($i=0;$i<$h-1;$i++){
                $im->floodfillPaintImage($transparentColor, 2500, $backgroundColor,$w-1,$i,false);
            }
            $fuzz = \Imagick::getQuantum() * 0.1; // 10%
            $im->transparentPaintImage($transparentColor, 0,$fuzz,false);
            $im->writeImage($imagePath.$outputpng);
            $im->destroy();
            $response['url'] = $imagePathUrl.$outputpng;
            $response['success'] = true;
        } else {
            echo 'here2'; exit;
            $response['success'] = false;
        }
        return $response;
    }

    public function getOrderAttachementImageDir()
    {
        return $this->_mediaDirectory->getAbsolutePath(self::ORDER_ATTACHMENT_IMAGE_PATH);
    }

    public function getOrderAttachementImageUrl()
    {
        $customerImagePath = str_replace('\\','/',self::ORDER_ATTACHMENT_IMAGE_PATH);
        return $this->getMediaUrl().$customerImagePath;
    }

    public function getDefaultAttributeIds()
    {
        return array(10,9,17,16);
    }
}

