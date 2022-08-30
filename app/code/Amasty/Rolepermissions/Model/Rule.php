<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Rolepermissions
 */

namespace Amasty\Rolepermissions\Model;

use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;

class Rule extends \Magento\Framework\Model\AbstractModel
{
    const PRODUCT_ACCESS_MODE_ANY = '';
    const PRODUCT_ACCESS_MODE_MY = 0;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;


    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        array $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_authSession = $authSession;
        $this->_productFactory = $productFactory;
        return parent::__construct($context, $registry, null, null, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Amasty\Rolepermissions\Model\ResourceModel\Rule');
        $this->setIdFieldName('id');
    }

    public function loadByRole($roleId)
    {
        if (!$roleId)
            return $this;

        $this->load($roleId, 'role_id');

        $fields = ['scope_storeviews', 'categories', 'products', 'scope_websites'];

        foreach ($fields as $field) {
            $data = $this->getData($field);
            if ($data == "") {
                $data = null;
            }
            else {
                $data = explode(',', $data);
            }

            $this->setData($field, $data);
        }

        $websites = $this->getScopeWebsites();
        
        if (!empty($websites)) {
            $stores = $this->_storeManager->getStores();

            $ids = [];

            foreach ($stores as $id => $store) {
                if (in_array($store->getWebsiteId(), $websites)) {
                    $ids []= $id;
                }
            }

            $this->setScopeStoreviews($ids);
        }

        return $this;
    }

    /*public function getScopeWebsites()
    {
        if($this->_corporate->isCorporateAdmin()) {
            return $this->_corporate->getRelevantWebsiteIds();
        }
        return [];
    }*/

    public function getPartiallyAccessibleWebsites()
    {
        if (!$this->hasData('partial_ws')) {

            if ($this->getScopeWebsites()) {
                $websites = $this->getScopeWebsites();
            }
            else if (!$this->getScopeStoreviews()) {
                $websites = array_keys($this->_storeManager->getWebsites());
            }
            else {
                $websitesMap = [];
                foreach ($this->_storeManager->getStores() as $store) {
                    if (in_array($store->getId(), $this->getScopeStoreviews())) {
                        $websitesMap[$store->getWebsiteId()] = true;
                    }
                }

                $websites = array_keys($websitesMap);
            }

            $this->setData('partial_ws', $websites);
        }

        return $this->getData('partial_ws');
    }

    public function restrictProductCollection(ProductCollection $collection)
    {
        $ruleConditions = [];
        $adapter = $this->getResource()->getConnection();
        $userId = $this->_authSession->getUser()->getId();
        $collection->addAttributeToSelect('amrolepermissions_owner', 'left');
        $allowOwn = false;

        if ($this->getProducts()) {
            if ($this->getProducts() != [self::PRODUCT_ACCESS_MODE_MY]) {
                $ruleConditions []= $adapter->quoteInto(
                    'e.entity_id IN (?)',
                    $this->getProducts()
                );
            }
            else {
                $allowOwn = true;
            }
        }

        if ($this->getCategories()) {
            $collection->getSelect()
                ->joinLeft(
                    ['cp' => $collection->getTable('catalog_category_product')],
                    'cp.product_id = e.entity_id',
                    []
                )
            ;

            $ruleConditions []= $adapter->quoteInto(
                'cp.category_id IN (?)',
                $this->getCategories()
            );
        }

        if ($this->getScopeStoreviews()) {
            $allowedWebsites = $this->getPartiallyAccessibleWebsites();

            $websiteTable = $collection
                ->getResource()
                ->getTable('catalog_product_website');

            $collection->getSelect()
                ->join(
                    ['am_product_website' => $websiteTable],
                    'am_product_website.product_id = e.entity_id',
                    []
                )
            ;
            $ruleConditions []= $adapter->quoteInto(
                'am_product_website.website_id IN (?)',
                $allowedWebsites
            );
        }

        $ownerCondition = $adapter->quoteInto(
            'at_amrolepermissions_owner.value = ?',
            $userId
        );

        if ($ruleConditions) {
            $ruleConditionsSql = implode(' AND ', $ruleConditions);
            $combinedCondition = "($ownerCondition OR ($ruleConditionsSql))";
            $collection->getSelect()->where($combinedCondition);
        }
        if ($allowOwn) {
            $collection->getSelect()->where($ownerCondition);
        }

        $collection->getSelect()->distinct();
    }

    public function getAllowedProductIds()
    {
        if ($this->getProducts() == self::PRODUCT_ACCESS_MODE_ANY) {
            return false;
        }
        else if ($this->getProducts() == [self::PRODUCT_ACCESS_MODE_MY]) {
            $userId = $this->_authSession->getUser()->getId();

            $collection = $this->_productFactory->create()->getCollection()
                ->addAttributeToFilter('amrolepermissions_owner', $userId)
            ;
            return $collection->getColumnValues('entity_id');
        }
        else {
            return $this->getProducts();
        }
    }

    public function checkProductOwner($product)
    {
        $userId = $this->_authSession->getUser()->getId();

        if ($product->getAmrolepermissionsOwner() == $userId)
            return true;

        return false;
    }

    public function checkProductPermissions($product)
    {
        if (!$this->getProducts())
            return true;

        if (!$product->getId())
            return true;

        return in_array($product->getId(), $this->getProducts());
    }

    public function getCollectionConfig()
    {
        if (!$this->hasData('collection_config')) {
            $config = [
                'external' => [
                    'store'   => [
                        'Magento\Cms\Model\ResourceModel\Page'                 => 'cms_page_store',
                        'Magento\Cms\Model\ResourceModel\Block'                => 'cms_block_store',
                        'Magento\Review\Model\ResourceModel\Rating'            => 'rating_store',
                        'Magento\Review\Model\ResourceModel\Review'            => 'review_store',
                        'Magento\Paypal\Model\ResourceModel\Billing\Agreement' => 'checkout_agreement_store',
                    ],
                    'website' => [
                        'Magento\CatalogRule\Model\ResourceModel\Rule' => 'catalogrule_website',
                        'Magento\SalesRule\Model\ResourceModel\Rule'   => 'salesrule_website',
                    ]
                ],
                'internal' => [
                    'store'   => [
                        'Magento\Widget\Model\ResourceModel\Widget\Instance' => 'store_ids',
                    ],
                    'website' => [
                        'Magento\Customer\Model\ResourceModel\Customer' => 'website_id'
                    ]
                ]
            ];

            if ($this->getLimitOrders()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order'] = 'main_table.store_id';
            }

            if ($this->getLimitInvoices()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Invoice'] = 'main_table.store_id';
            }

            if ($this->getLimitShipments()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Shipment'] = 'main_table.store_id';
            }

            if ($this->getLimitMemos()) {
                $config['internal']['store']['Magento\Sales\Model\ResourceModel\Order\Creditmemo'] = 'main_table.store_id';
            }

            $this->setData('collection_config', $config);
        }

        return $this->getData('collection_config');
    }
}
