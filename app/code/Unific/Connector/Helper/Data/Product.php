<?php

namespace Unific\Connector\Helper\Data;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Product
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Converter
     */
    protected $outputProcessor;

    /**
     * @var ProductInterface
     */
    protected $product;

    /**
     * @var array
     */
    protected $returnData = [];

    protected $mainProductAttributes = [
        'id'           => 'entity_id',
        'name'         => 'name',
        'price'        => 'price',
        'sku'          => 'sku',
        'url_key'      => 'url_key',
        'category_ids' => 'category_ids',
        'image'        => 'image',
        'description'  => 'description'
    ];

    /**
     * ProductPlugin constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param Converter $outputProcessor
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Converter $outputProcessor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * @param ProductInterface $product
     */
    public function setProduct(ProductInterface $product)
    {
        $this->product = $product;
        $this->setProductInfo();
    }

    /**
     * @return ProductInterface
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @return void
     */
    protected function setProductInfo()
    {
        $this->returnData = $this->outputProcessor->convertValue($this->product, ProductInterface::class);

        foreach ($this->mainProductAttributes as $fieldName => $attributeName) {
            $this->returnData[$fieldName] = $this->product->getData($attributeName);
        }

        $this->returnData['product_url_suffix'] = $this->scopeConfig->getValue(
            ProductUrlPathGenerator::XML_PATH_PRODUCT_URL_SUFFIX,
            ScopeInterface::SCOPE_STORE,
            $this->product->getStoreId()
        );
    }

    /**
     * @return array
     */
    public function getProductInfo()
    {
        return $this->returnData;
    }
}
