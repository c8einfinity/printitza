<?php
namespace Designnbuy\Template\Block\Adminhtml\Template;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;

class Tool extends \Magento\Backend\Block\Template
{
    protected $_template = 'template/tool.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Template factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Designnbuy\Base\Helper\Data
     */

    private $dnbBaseHelper;
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
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_templateFactory = $templateFactory;
        $this->dnbBaseHelper = $dnbBaseHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */

    public function getCurrentTemplate()
    {
        return $template = $this->_initTemplate();
    }


    /**
     * Init Template
     *
     * @return \Designnbuy\Template\Model\Template || false
     */
    protected function _initTemplate()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();
        $template = $this->_templateFactory->create();
        $template->setStoreId($storeId);
        $template->load($id);
        return $template;
    }


    public function getSvgPath()
    {
        return $this->dnbBaseHelper->getTemplateSVGDir();
    }

    public function getMediaPath()
    {
        return $this->dnbBaseHelper->getMediaUrl();
    }

    public function getImageUploadPath()
    {
        return $this->dnbBaseHelper->getCustomerImageDir();
    }

}