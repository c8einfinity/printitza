<?php
namespace Designnbuy\Designidea\Block\Adminhtml\Designidea;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;

class Tool extends \Magento\Backend\Block\Template
{
    protected $_template = 'designidea/tool.phtml';

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
     * Catalog category factory
     *
     * @var \Designnbuy\Merchandise\Model\MerchandiseFactory
     */
    protected $_merchandise;

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
        \Designnbuy\Designidea\Model\DesignideaFactory $designideaFactory,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_designideaFactory = $designideaFactory;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_merchandise = $merchandise;
        parent::__construct($context, $data);
    }

    public function getProductId()
    {
        $productId = $this->getRequest()->getParam('id');
        if($productId == '') {
            $productId = $this->_merchandise->getDefaultProduct();
        }
        return $productId;
    }
    /**
     * @return void
     */

    public function getCurrentDesign()
    {
        return $template = $this->_initDesign();
    }


    /**
     * Init Template
     *
     * @return \Designnbuy\Template\Model\Template || false
     */
    protected function _initDesign()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->getRequest()->getParam('store', 0);
        $designidea = $this->_designideaFactory->create();
        $designidea->load($id);
        $designidea->setStoreId($storeId);
        return $designidea;
    }


    public function getSvgPath()
    {
        return $this->dnbBaseHelper->getDesignIdeaSVGDir();
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