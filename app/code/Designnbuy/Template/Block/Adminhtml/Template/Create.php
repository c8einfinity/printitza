<?php
namespace Designnbuy\Template\Block\Adminhtml\Template;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;

class Create extends \Magento\Backend\Block\Template
{
    protected $_template = 'template/create.phtml';
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\ConfigurableProduct\Model\Product\Type\VariationMatrix $variationMatrix
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Image $image
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param LocatorInterface $locator
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
    public function getIframeUrl() {
        $params = [];
        $request = $this->getRequest()->getParams();
        if (isset($request['id'])) {
            $templateId = $request['id'];
            $params['id'] = $templateId;
        }
        if (isset($request['store'])) {
            $storeId = $request['store'];
            $params['store'] = $storeId;
        }
        return $this->getUrl('template/template/tool', $params);

    }
}