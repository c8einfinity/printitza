<?php
namespace Designnbuy\HomePageCategory\Block\Home;
use Magento\Catalog\Api\CategoryRepositoryInterface;
class CategoryList extends \Magento\Framework\View\Element\Template {

    protected $_categoryCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;
    /**
     * Initialize
     *
     * @param Magento\Catalog\Block\Product\Context $context
     * @param Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository,
     * @param Magento\Catalog\Model\ResourceModel\Product\Collection $collection
     * @param Magento\Framework\Url\Helper\Data $urlHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
		array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->_categoryHelper = $categoryHelper;
        parent::__construct(
            $context,$data
        );
    }


    public function getHomePageCategryList()
    {
        $store = $this->_storeManager->getStore();
        $collection = $this->_categoryCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('custom_image')
            ->addAttributeToSelect('display_on_homepage')
            ->addAttributeToSelect('description')
            ->addAttributeToFilter('display_on_homepage', '1')
            ->setStore($this->_storeManager->getStore())
            ->addIsActiveFilter()
            //->addLevelFilter(10)
            ->setOrder('position','ASC')
            ->load();

        //$categoryCollection = clone $collection;
        //$categoryCollection->setOrder('position','ASC');
        //echo $collection->getSelect()->__toString();
        return $collection;
    }

    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @param string $attributeCode
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomImageUrl($category)
    {
        $url = false;
        $image = $category->getCustomImage();
        if ($image) {
            if (is_string($image)) {
                $url = $this->_storeManager->getStore()->getBaseUrl(
                        \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                    ) . 'catalog/category/' . $image;
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Something went wrong while getting the image url.')
                );
            }
        }
        return $url;
    }

}
