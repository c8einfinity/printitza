<?php
namespace Designnbuy\Customer\Model\Autocomplete;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaFactory as FullTextSearchCriteriaFactory;
use Magento\Framework\Api\Search\SearchInterface as FullTextSearchApi;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Payment\Gateway\Http\Client\Zend;
use Magento\Search\Model\Autocomplete\DataProviderInterface;
use Magento\Search\Model\Autocomplete\ItemFactory;
use Magento\Search\Model\QueryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use \Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfig;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Customer\Model\Session;
use Designnbuy\Customer\Model\GroupFactory;


class SearchDataProvider implements DataProviderInterface
{

    /**
     * Autocomplete limit
     */
    const CONFIG_AUTOCOMPLETE_LIMIT = 'catalog/search/autocomplete_limit';

    /**
     * Limit
     *
     * @var int
     */
    protected $limit;

    /** @var QueryFactory */
    protected $queryFactory;

    /** @var ItemFactory */
    protected $itemFactory;

    /** @var \Magento\Framework\Api\Search\SearchInterface */
    protected $fullTextSearchApi;

    /** @var FullTextSearchCriteriaFactory */
    protected $fullTextSearchCriteriaFactory;

    /** @var FilterGroupBuilder */
    protected $searchFilterGroupBuilder;

    /** @var FilterBuilder */
    protected $filterBuilder;

    /** @var ProductRepositoryInterface */
    protected $productRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var ProductHelper
     */
    protected $productHelper;

    /** @var \Magento\Catalog\Helper\Image */
    protected $imageHelper;

    /**
     * @var SortOrderBuilder
     */
    protected $_sortOrderBuilder;

    /**
     * @var Visibility
     */
    protected $_productVisibility;

    /**
     * @var CategoryCollection
     */
    protected $_categoryFactory;

    /**
     * @var CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * SearchDataProvider constructor.
     * @param QueryFactory $queryFactory
     * @param ItemFactory $itemFactory
     * @param ScopeConfig $scopeConfig
     * @param FullTextSearchApi $search
     * @param FullTextSearchCriteriaFactory $searchCriteriaFactory
     * @param FilterGroupBuilder $searchFilterGroupBuilder
     * @param FilterBuilder $filterBuilder
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param StoreManagerInterface $storeManager
     * @param PriceCurrencyInterface $priceCurrency
     * @param Image $imageHelper
     * @param SortOrderBuilder $sortOrderBuilder
     * @param Data $helper
     * @param Visibility $productVisibility
     * @param CategoryCollection $categoryFactory
     * @param CategoryRepositoryInterface $categoryRepository
     */
    public function __construct(
        QueryFactory $queryFactory,
        ItemFactory $itemFactory,
        ScopeConfig $scopeConfig,
        FullTextSearchApi $search,
        FullTextSearchCriteriaFactory $searchCriteriaFactory,
        FilterGroupBuilder $searchFilterGroupBuilder,
        FilterBuilder $filterBuilder,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrency,
        Image $imageHelper,
        SortOrderBuilder $sortOrderBuilder,
        Visibility $productVisibility,
        CategoryCollection $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        Session $customerSession,
        GroupFactory $groupFactory
    )
    {
        $this->queryFactory = $queryFactory;
        $this->itemFactory = $itemFactory;
        $this->fullTextSearchApi = $search;
        $this->fullTextSearchCriteriaFactory = $searchCriteriaFactory;
        $this->filterBuilder = $filterBuilder;
        $this->searchFilterGroupBuilder = $searchFilterGroupBuilder;
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->storeManager = $storeManager;
        $this->priceCurrency = $priceCurrency;
        $this->imageHelper = $imageHelper;
        $this->_sortOrderBuilder = $sortOrderBuilder;
        $this->_productVisibility = $productVisibility;
        $this->_categoryFactory = $categoryFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->session = $customerSession;
        $this->_group = $groupFactory;

        $this->limit = (int) $scopeConfig->getValue(
            self::CONFIG_AUTOCOMPLETE_LIMIT,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array|\Magento\Search\Model\Autocomplete\ItemInterface[]
     */
    public function getItems()
    {
        $results = $items = $result = [];
        $queryFactory = $this->queryFactory->get();
        $query = $queryFactory->getQueryText();
        $collection = $queryFactory->getSuggestCollection();

        $productIds = $this->searchProducts($query);
        $resultItem[] = $this->itemFactory->create([
            'title' => __('No suggestions found'),
            'num_results' => 0,
        ]);
        
        // Customer group wise product customization.

        if ($this->session->isLoggedIn()) {
            if($this->session->getCustomer()->getGroupId()){
                $groupInfo = $this->_group->create();
                $groupInfo->load($this->session->getCustomer()->getGroupId());
                
                if($groupInfo->getProducts()){
                    $groupProd = explode("&",$groupInfo->getProducts());
                    if(!empty($groupProd)){
                        $productIds = array_intersect($groupProd,$productIds);
                    }

                }
            }
        } else {
            $groupInfo = $this->_group->create();
            $groupInfo->load(0);
            if($groupInfo->getProducts()){
                $groupProd = explode("&",$groupInfo->getProducts());
                if(!empty($groupProd)){
                    $productIds = array_intersect($groupProd,$productIds);
                }
            }
        }
        
        //echo "<pre>"; print_r($productIds); exit;
        // Customer group wise product customization end.

        if ($productIds) {
            $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $productIds, 'in')->create();
            $products = $this->productRepository->getList($searchCriteria);

            $productsArr = $products->getItems();
            $sorted = $this->_reOrderArr($productIds, $productsArr);
            $storeId = (int) $this->storeManager->getStore()->getId();

            foreach ($sorted as $product) {
                $image = $this->imageHelper->init($product, 'product_page_image_small')->resize(80)->getUrl();
                $resultItem = $this->itemFactory->create([
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'price' => $this->priceCurrency->format($product->getPriceInfo()->getPrice('regular_price')->getAmount()->getValue(), false),
                    'special_price' => $this->priceCurrency->format($product->getPriceInfo()->getPrice('special_price')->getAmount()->getValue(), false),
                    'final_price' => $this->priceCurrency->format($product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue(), false),
                    'has_special_price' => $product->getSpecialPrice() > 0 ? true : false,
                    'image' => $image,
                    'description' => $product->getDescription(),
                    'url' => $product->getProductUrl(),
                ]);
                $items[] = $resultItem;
            }

            $result[] = $resultItem;

            if ($collection->getSize() >= 1) {
                $result = [];
                foreach ($collection as $item) {
                    $resultItem = $this->itemFactory->create([
                        'title' => $item->getQueryText(),
                        'num_results' => $item->getNumResults(),
                    ]);
                    if ($resultItem->getTitle() == $query) {
                        array_unshift($result, $resultItem);
                    } else {
                        $result[] = $resultItem;
                    }
                }
            } else {
                $result = [];
                $resultItem = $this->itemFactory->create([
                    'title' => 'No suggestions found',
                    'num_results' => ''
                ]);
                array_unshift($result, $resultItem);
            }

            $results = array_merge($items, $result);

        }

        $response = (!empty($results)) ? $results : $resultItem;

        return $response;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCategoryItems()
    {
        $results = [];
        $size = 3;
        $queryFactory = $this->queryFactory->get();
        $query = $queryFactory->getQueryText();
        $categoryCollection = $this->_categoryFactory;
        $categoryCollection->addAttributeToSelect('*');
        if($size) {
            $categoryCollection->setPageSize($size);
        }
        $categoryCollection->addAttributeToFilter('name',['like' => '%'.$query.'%'])
                           ->addAttributeToFilter('is_active',1)
                           ->setStore($this->storeManager->getStore());

        if(!$categoryCollection->getSize()) {
            $results['nores'] = 1;
            return $results;
        }

        foreach($categoryCollection as $catItem){
            $category = $this->_categoryRepository->get($catItem->getId(), $this->storeManager->getStore()->getId());
            if($category) {
                $parents = $category->getParentCategories();
                $parentsArr = [];

                foreach($parents as $parent) {
                    if($parent->getId() != $category->getId()){
                        $parentsArr[] = $parent->getName();
                    }
                }

                $results[$category->getId()]['parents'] = $parentsArr;
                $results[$category->getId()]['name'] = $category->getName();
                $results[$category->getId()]['url'] = $category->getUrl();
            }
        }

        return $results;
    }

    /**
     * @param $query
     * @return array
     */
    protected function searchProducts($query)
    {
        $searchCriteria = $this->fullTextSearchCriteriaFactory->create();
        $searchCriteria->setRequestName('quick_search_container');
        $termFilter = $this->filterBuilder->setField('search_term')->setValue($query)->setConditionType('like')->create();
        $visibilityFilter = $this->filterBuilder->setField('visibility')->setValue($this->_productVisibility->getVisibleInSearchIds())->setConditionType('in')->create();
        $filterGroup = $this->searchFilterGroupBuilder->addFilter($termFilter)->addFilter($visibilityFilter)->create();
        $sortOrder = $this->_sortOrderBuilder->setField('relevance')->setDirection(SortOrder::SORT_DESC)->create();
        $searchCriteria->setFilterGroups([$filterGroup]);
        $searchCriteria->setSortOrders([$sortOrder]);
        //$searchCriteria->setPageSize($limit);
        $searchResults = $this->fullTextSearchApi->search($searchCriteria);
        $productIds = [];

        foreach ($searchResults->getItems() as $searchDocument) {
            $productIds[] = $searchDocument->getId();
        }

        return $productIds;
    }

    /**
     * @param $ids
     * @param $collectionArr
     * @return mixed
     */
    private function _reOrderArr($ids, $collectionArr)
    {
        foreach ($ids as $k => $v) {
            $val = $collectionArr[$v];
            $result[$v] = $val;
        }

        return $result;
    }
}