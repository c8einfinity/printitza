<?php

namespace Designnbuy\Designidea\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\AbstractResource;
use Magento\TestFramework\Event\Magento;

/**
 * Designidea entity resource model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends AbstractResource
{
    /**
     * Product to website linkage table
     *
     * @var string
     */
    protected $_categoryWebsiteTable;
    /**
     * Store firstly set attributes to filter selected attributes when used specific store_id
     *
     * @var array
     */
    protected $_attributes   = array();
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Model factory
     *
     * @var \Magento\Catalog\Model\Factory
     */
    protected $_modelFactory;
    /**
     * Designidea Product factory.
     *
     * @var \Designnbuy\Designidea\Model\Designidea\ProductFactory
     */
    public $_designideaProductFactory;

    protected $_productInstance = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;
    /**
     * @var false|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    protected $eavConfig;
    /**
     * @param \Magento\Eav\Model\Entity\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Factory $modelFactory
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Factory $modelFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Category\Product $designideaProductFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\Config $eavConfig,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_designideaProductFactory = $designideaProductFactory;
        parent::__construct($context, $storeManager, $modelFactory, $data);
        $this->setType(\Designnbuy\Designidea\Model\Category::ENTITY);
        $this->connectionName  = 'category';
        $this->setConnection('category_read', 'category_write');
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->eavConfig = $eavConfig;
    }
    /**
     * Entity type getter and lazy loader
     *
     * @return \Magento\Eav\Model\Entity\Type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(\Designnbuy\Designidea\Model\Category::ENTITY);
        }
        return parent::getEntityType();
    }

    /**
     * Process category data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\DataObject $object)
    {

        $identifierGenerator = \Magento\Framework\App\ObjectManager::getInstance()
            ->create('Designnbuy\Designidea\Model\ResourceModel\PageIdentifierGenerator');

        $identifierGenerator->generate($object);

        if (!$this->isValidPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The category URL key contains disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The category URL key cannot be made of only numbers.')
            );
        }

        return parent::_beforeSave($object);
    }


    /**
     *  Check whether category identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

    /**
     *  Check whether category identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^([^?#<>@!&*()$%^\\/+=,{}]+)?$/', $object->getData('identifier'));
    }

    /**
     * Initialize attribute value for object
     *
     * @param Magento\Catalog\Model\AbstractModel $object
     * @param array $valueRow
     * @return Magento\Catalog\Model\ResourceModel\AbstractResource
     */
    /*protected function _setAttributeValue($object, $valueRow)
    {
        $attribute = $this->getAttribute($valueRow['attribute_id']);
        if ($attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $isDefaultStore = $valueRow['store_id'] == $this->getDefaultStoreId();
            if (isset($this->_attributes[$valueRow['attribute_id']])) {
                if ($isDefaultStore) {
                    $object->setAttributeDefaultValue($attributeCode, $valueRow['value']);
                } else {
                    $object->setAttributeDefaultValue(
                        $attributeCode,
                        $this->_attributes[$valueRow['attribute_id']]['value']
                    );
                }
            } else {
                $this->_attributes[$valueRow['attribute_id']] = $valueRow;
            }

            $value   = $valueRow['value'];
            $valueId = $valueRow['value_id'];

            $object->setData($attributeCode, $value);
            if (!$isDefaultStore) {
                $object->setExistsStoreValueFlag($attributeCode);
            }
            $attribute->getBackend()->setEntityValueId($object, $valueId);
        }

        return $this;
    }*/

    /**
     * Reset firstly loaded attributes
     *
     * @param Varien_Object $object
     * @param integer $entityId
     * @param array|null $attributes
     * @return Magento\Catalog\Model\ResourceModel\AbstractResource
     */
    /*public function load($object, $entityId, $attributes = array())
    {
        $this->_attributes = array();
        return parent::load($object, $entityId, $attributes);
    }*/
    /**
     * get product relation model
     *
     * @access public
     * @return Designnbuy\Designidea\Model\Designidea\Product
     * @author Ajay Makwana
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = $this->_designideaProductFactory;
        }
        return $this->_productInstance;
    }

    /**
     * save product designidea relation
     *
     * @access public
     * @return Designnbuy\Designidea\Model\Category
     * @author Ajay Makwana
     */
    /**
     * Get store ids to which specified item is assigned
     * @access public
     * @param int $designideaId
     * @return array
     * @author Ajay Makwana
     */
    protected function _afterSave(\Magento\Framework\DataObject $object)
    {
        $this->_saveWebsiteIds($object);
        $data = $object->getProductsData();

        if (!is_null($data)) {
            $this->getProductInstance()->saveProductDesignIdeaCategoryRelation($object, $data);
        }
        return parent::_afterSave($object);
    }

    /**
     * Save category website relations
     *
     * @param Designnbuy\Designidea\Model\Category $category
     * @return $this
     */
    protected function _saveWebsiteIds($category)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            $id = $this->_storeManager->getDefaultStoreView()->getWebsiteId();
            $category->setWebsiteIds([$id]);
        }
        $websiteIds = $category->getWebsiteIds();

        $oldWebsiteIds = [];

        $category->setIsChangedWebsites(false);

        $connection = $this->getConnection();

        $oldWebsiteIds = $this->getWebsiteIds($category);

        $insert = array_diff($websiteIds, $oldWebsiteIds);
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $websiteId) {
                if($websiteId != 0){
                    $data[] = ['category_id' => (int)$category->getEntityId(), 'website_id' => (int)$websiteId];
                }
            }

            if(!empty($data)){
                $connection->insertMultiple($this->getCategoryWebsiteTable(), $data);
            }
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = ['category_id = ?' => (int)$category->getEntityId(), 'website_id = ?' => (int)$websiteId];

                $connection->delete($this->getCategoryWebsiteTable(), $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $category->setIsChangedWebsites(true);
        }

        return $this;
    }

    /**
     * Retrieve designidea related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_designidea_relatedproduct')],
            'e.entity_id = rl.related_id',
            ['position']
        )->where(
            'rl.designidea_id = ?',
            $this->getId()
        );
        return $collection;
    }

    /**
     * Check if category identifier exist for specific store
     * return page id if page exists
     *
     * @param string $identifier
     * @param int|array $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeIds, $websiteIds)
    {
        if (!is_array($websiteIds)) {
            $websiteIds = [$websiteIds];
        }

        $websiteIds[] = 1;

        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }

        $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        $select = $this->_getLoadByIdentifierSelect($identifier, $storeIds, $websiteIds, 1);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('cp.entity_id')->order('cps.website_id DESC')->limit(1);

        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Check if category identifier exist for specific store
     * return category id if category exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    protected function _getLoadByIdentifierSelect($identifier, $storeIds, $websiteIds, $isActive = null)
    {
        /*$select = $this->getConnection()->select()->from(
            ['cp' => $this->getTable('designnbuy_designidea_category_entity')]
        )->join(
            ['cps' => $this->getTable('designnbuy_designidea_category_website')],
            'cp.entity_id = cps.category_id',
            []
        )->where(
            'cp.identifier = ?',
            $identifier
        )->where(
            'cps.website_id IN (?)',
            $websiteIds
        );

        if (!is_null($isActive)) {
            $select->where('cp.status = ?', $isActive);
        }*/

        $urlRewrite = $this->eavConfig->getAttribute('designnbuy_designidea_category', 'identifier');
        if (!$urlRewrite || !$urlRewrite->getId()) {
            return false;
        }
        $identifierTable = $urlRewrite->getBackend()->getTable();

        $status = $this->eavConfig->getAttribute('designnbuy_designidea_category', 'status');
        if (!$status || !$status->getId()) {
            return false;
        }
        $statusTable = $status->getBackend()->getTable();

        $select = $this->getConnection()->select()->from(
            ['cp' => $this->getTable('designnbuy_designidea_category_entity')]
        )->join(
            ['id' => $identifierTable],
            'cp.entity_id = id.entity_id',
            ['id.identifier']
        )->join(
            ['st' => $statusTable],
            'cp.entity_id = st.entity_id',
            ['st.status']
        )->join(
            ['cps' => $this->getTable('designnbuy_designidea_category_website')],
            'cp.entity_id = cps.category_id',
            []
        )->where(
            'id.value = ?',
            $identifier
        )->where(
            'cps.website_id IN (?)',
            $websiteIds
        );

        if (!is_null($isActive)) {
            $select->where('st.value = ?', $isActive);
        }


        /*$select = $this->getConnection()->select()
            ->from(array('e' => $table))
            ->where('e.attribute_id = ?', $urlRewrite->getId())
            ->where('e.value = ?', $urlKey)
            ->where('e.store_id IN (?)', $store)
            ->order('e.store_id DESC');
        return $select;*/

        return $select;
    }    
    

    /**
     * Retrieve product website identifiers
     *
     * @param \Magento\Catalog\Model\Product|int $product
     * @return array
     */
    public function getWebsiteIds($category)
    {
        $connection = $this->getConnection();

        if ($category instanceof \Designnbuy\Designidea\Model\Category) {
            $categoryId = $category->getEntityId();
        } else {
            $categoryId = $category;
        }

        $select = $connection->select()->from(
            $this->getCategoryWebsiteTable(),
            'website_id'
        )->where(
            'category_id = ?',
            (int)$categoryId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Product Website table name getter
     *
     * @return string
     */
    public function getCategoryWebsiteTable()
    {
        if (!$this->_categoryWebsiteTable) {
            $this->_categoryWebsiteTable = $this->getTable('designnbuy_designidea_category_website');
        }
        return $this->_categoryWebsiteTable;
    }

    public function getCategoryDesigns()
    {
        $this->getResource()->getCollection();
    }
}