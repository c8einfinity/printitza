<?php
namespace Designnbuy\Reseller\Model;


class Collections extends \Designnbuy\Reseller\Model\Observer\AbstractClass {

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager = null;

    public function __construct(
        \Designnbuy\Reseller\Model\Admin $reseller,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\ObjectManager\FactoryInterface $factory,
        \Magento\Framework\ObjectManager\ConfigInterface $config

    )
    {
        $this->_storeManager = $storeManager;
        $this->_objectManager = $objectManager;
        parent::__construct($reseller);
    }


    /**---Functions---*/
    public function limitStores($collection)
    {
        if($this->_reseller->isResellerAdmin()) {
            $collection->addGroupFilter(array_merge($this->_reseller->getStoreGroupIds(), array(0)));
        }
    }


    public function limitWebsites($collection)
    {
        if($this->_reseller->isResellerAdmin()) {
            $collection->addIdFilter(array_merge($this->_reseller->getRelevantWebsiteIds(), array(0)));
            $collection->addFilterByGroupIds(array_merge($this->_reseller->getStoreGroupIds(), array(0)));
        }
    }


    public function limitStoreGroups($collection)
    {
        if($this->_reseller->isResellerAdmin()) {
            $collection->addFieldToFilter('group_id',
                array('in' => array_merge($this->_reseller->getStoreGroupIds(), array(0)))
            );
        }
    }


    public function addStoreFilterNoAdmin($collection)
    {
        if($this->_reseller->isResellerAdmin()) {
            $collection->addStoreFilter($this->_reseller->getStoreIds(), false);
        }
    }


    public function addStoreFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addStoreFilter($this->_reseller->getStoreIds(), false);
        }
    }

    public function addAttributeToFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addAttributeToFilter('store_id', $this->_reseller->getStoreIds());
        }
    }


    public function limitProducts($collection)
    {
        if($this->_reseller->isResellerAdmin()) {
            $relevantWebsiteIds = $this->_reseller->getRelevantWebsiteIds();

            $websiteIds = array();
            $filters = $collection->getLimitationFilters();

            if (isset($filters['website_ids'])) {
                $websiteIds = (array)$filters['website_ids'];
            }
            if (isset($filters['store_id'])) {
                $websiteIds[] = $this->_storeManager->getStore($filters['store_id'])->getWebsiteId();

            }

            if (count($websiteIds)) {
                $collection->addWebsiteFilter(array_intersect($websiteIds, $relevantWebsiteIds));
            } else {
                $collection->addWebsiteFilter($relevantWebsiteIds);
            }

            if(count($relevantWebsiteIds)){
                $webSite = $this->_storeManager->getWebsite($relevantWebsiteIds[0]);
                $collection->setStoreId($webSite->getDefaultGroupId());
            }

        }
    }


    public function limitCustomers($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addAttributeToFilter(
                'website_id',
                array('website_id' => array('in' => $this->_reseller->getRelevantWebsiteIds()))
            );
        }
    }


    /**
     * Limit customers collection
     *
     * @param \Magento\Customer\Model\ResourceModel\Grid\Collection $collection
     * @return void
     */
    public function addCustomerWebsiteFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addFieldToFilter(
                'website_id',
                ['website_id' => ['in' => $this->_reseller->getRelevantWebsiteIds()]]
            );
        }
    }

    public function addTemplateWebsiteFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addWebSiteFilter(
                $this->_reseller->getRelevantWebsiteIds()
            );
        }
    }

    public function addDesignideaWebsiteFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addWebSiteFilter(
                $this->_reseller->getRelevantWebsiteIds()
            );
        }
    }




    public function limitReviews($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds());
    }


    public function limitProductReviews($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }


    public function limitOnlineCustomers($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitGiftCardAccounts($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitRewardHistoryWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitRewardBalanceWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitStoreCredits($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitStoreCreditsHistory($collection)
    {
        $collection->addWebsitesFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitCatalogEvents($collection)
    {
        $collection->capByCategoryPaths($this->_role->getAllowedRootCategories());
    }


    public function limitCatalogCategories($collection)
    {
        $collection->addPathsFilter($this->_role->getAllowedRootCategories());
    }


    public function limitCoreUrlRewrites($collection)
    {
        $collection->addStoreFilter($this->_role->getStoreIds(), false);
    }


    public function limitRatings($collection)
    {
        $collection->setStoreFilter($this->_role->getStoreIds());
    }


    public function addStoreAttributeToFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addAttributeToFilter('store_id', array('in' => $this->_reseller->getStoreIds()));
        }

    }


    /**
     * Add store_id field to filter
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @return void
     */
    public function addStoreFieldToFilter($collection)
    {
        if($this->_reseller->isResellerAdmin()){
            $collection->addFieldToFilter('store_id', array('in' => $this->_reseller->getStoreIds()));
        }
    }


    public function limitCheckoutAgreements($collection)
    {
        $collection->setIsStoreFilterWithAdmin(false)->addStoreFilter($this->_role->getStoreIds());
    }


    public function limitAdminPermissionRoles($collection)
    {
        $limited = $this->_objectManager->get('Enterprise\Enterprise\Admingws\Model\Collections')
            ->getRolesOutsideLimitedScope(
                $this->_role->getIsAll(),
                $this->_role->getWebsiteIds(),
                $this->_role->getStoreGroupIds()
            );

        $collection->addFieldToFilter('role_id', array('nin' => $limited));
    }


    public function limitAdminPermissionUsers($collection)
    {
        $limited = $this->_objectManager->get('Enterprise\Enterprise\Admingws\Model\Collections')
            ->getUsersOutsideLimitedScope(
                $this->_role->getIsAll(),
                $this->_role->getWebsiteIds(),
                $this->_role->getStoreGroupIds()
            );
        $collection->addFieldToFilter('user_id', array('nin' => $limited));
    }


    public function addSalesSaleCollectionStoreFilter($observer)
    {
        $collection = $observer->getEvent()->getCollection();

        $this->addStoreFilter($collection);
    }


    public function rssOrderNewCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->addStoreAttributeToFilter($collection);
        return $this;
    }


    protected function _initRssAdminRole()
    {
        
        $rssSession = $this->_objectManager->get('Magento\Rss\Model\Session');
        
        $adminUser = $rssSession->getAdmin();
        if ($adminUser) {
            $this->_role->setAdminRole($adminUser->getRole());
        }
        return $this;
    }


    public function rssCatalogNotifyStockCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_initRssAdminRole()->limitProducts($collection);
        return $this;
    }


    public function rssCatalogReviewCollectionSelect($observer)
    {
        $collection = $observer->getEvent()->getCollection();
        $this->_initRssAdminRole()->limitProducts($collection);
        return $this;
    }


    public function limitProductReports($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds(), $this->_role->getRelevantWebsiteIds());
    }


    public function limitGiftRegistryEntityWebsites($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitBestsellersCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }


    public function limitMostViewedCollection($collection)
    {
        $collection->addStoreRestrictions($this->_role->getStoreIds());
    }


    public function limitRuleEntityCollection($collection)
    {
        $collection->addWebsiteFilter($this->_role->getRelevantWebsiteIds());
    }


    public function limitCustomerSegments($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }


    public function limitPriceRules($collection)
    {
        $this->limitRuleEntityCollection($collection);
    }




}
