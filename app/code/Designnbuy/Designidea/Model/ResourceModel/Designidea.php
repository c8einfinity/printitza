<?php

namespace Designnbuy\Designidea\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\AbstractResource;

/**
 * Designidea entity resource model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Designidea extends AbstractResource
{
    /**
     * Product to website linkage table
     *
     * @var string
     */
    protected $_designideaWebsiteTable;
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
     * Designidea Upsell factory.
     *
     * @var \Designnbuy\Designidea\Model\Upsell\DesignideaFactory
     */
    public $_designideaUpsellFactory;

    /**
     * Designidea Crosssell factory.
     *
     * @var \Designnbuy\Designidea\Model\Crosssell\DesignideaFactory
     */
    public $_designideaCrosssellFactory;

    protected $_upsellInstance = null;

    protected $_crosssellInstance = null;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\PageIdentifierGenerator
     */
    protected $_pageIdentifierGenerator;

    /**
     * @param \Magento\Eav\Model\Entity\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Factory $modelFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Factory $modelFactory,
        \Designnbuy\Designidea\Model\Designidea\Product $designideaProductFactory,
        \Designnbuy\Designidea\Model\Designidea\Upsell $templateUpsellFactory,
        \Designnbuy\Designidea\Model\Designidea\Crosssell $templateCrosssellFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell\CollectionFactory $upsellCollectionFactory,
        \Designnbuy\Template\Model\ResourceModel\PageIdentifierGenerator $pageIdentifierGenerator,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_designideaProductFactory = $designideaProductFactory;
        $this->_designideaUpsellFactory = $templateUpsellFactory;
        $this->_designideaCrosssellFactory = $templateCrosssellFactory;
        parent::__construct($context, $storeManager, $modelFactory, $data);
        $this->setType(\Designnbuy\Designidea\Model\Designidea::ENTITY);
        $this->connectionName  = 'designidea';
        $this->setConnection('designidea_read', 'designidea_write');
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_pageIdentifierGenerator = $pageIdentifierGenerator;
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
            $this->setType(\Designnbuy\Designidea\Model\Designidea::ENTITY);
        }
        return parent::getEntityType();
    }
    /**
     * Process category data before saving
     * prepare path and increment children count for parent categories
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _beforeSave(\Magento\Framework\DataObject $object)
    {
        parent::_beforeSave($object);

        // Url Identifier
        $identifierGenerator = $this->_pageIdentifierGenerator;
        $identifierGenerator->generate($object);

        if (!$this->isValidPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The template URL key contains disallowed symbols.')
            );
        }

        if ($this->isNumericPageIdentifier($object)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('The template URL key cannot be made of only numbers.')
            );
        }
        // End

        $typeId = $object->getTypeId();
        $object->setTypeId($typeId);
        $this->_saveCategoryIds($object);
        return $this;
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
     * get upsell template model
     *
     * @access public
     * @return Designnbuy\Template\Model\Template\Upsell
     */
    public function getUpsellInstance()
    {
        if (!$this->_upsellInstance) {
            $this->_upsellInstance = $this->_designideaUpsellFactory;
        }
        return $this->_upsellInstance;
    }

    /**
     * get upsell template model
     *
     * @access public
     * @return Designnbuy\Template\Model\Template\Upsell
     */
    public function getCrosssellInstance()
    {
        if (!$this->_crosssellInstance) {
            $this->_crosssellInstance = $this->_designideaCrosssellFactory;
        }
        return $this->_crosssellInstance;
    }

    /**
     * save product designidea relation
     *
     * @access public
     * @return Designnbuy\Designidea\Model\Designidea
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
        $this->_saveTypeId($object);
        $this->_saveWebsiteIds($object);
        $this->getProductInstance()->saveProductDesignIdeaRelation($object);
        $this->getUpsellInstance()->saveUpsellDesignideaRelation($object);
        $this->getCrosssellInstance()->saveCrosssellDesignideaRelation($object);
        return parent::_afterSave($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\DataObject $object)
    {
        if ($object->getId()) {
            $categories = [];
            $categories = explode(',', $object->getCategoryId());
            $object->setCategoryId($categories);
        }

        return parent::_afterLoad($object);
    }

    /**
     * Update path field
     *
     * @param \Magento\Catalog\Model\Category $object
     * @return $this
     */
    protected function _saveTypeId($object)
    {
        if ($object->getId()) {
            $this->getConnection()->update(
                $this->getEntityTable(),
                ['type_id' => $object->getTypeId()],
                ['entity_id = ?' => $object->getId()]
            );
        }
        return $this;
    }

    protected function _saveCategoryIds($object)
    {
        if(is_array($object->getCategoryId()) && !empty($object->getCategoryId())){
            $categories =  implode(',', $object->getCategoryId());
            $object->setCategoryId($categories);
        }

    }

    /**
     * Save Designidea website relations
     *
     * @param Designnbuy\Designidea\Model\Designidea $designidea
     * @return $this
     */
    protected function _saveWebsiteIds($designidea)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            $id = $this->_storeManager->getDefaultStoreView()->getWebsiteId();
            $designidea->setWebsiteIds([$id]);
        }
        $websiteIds = $designidea->getWebsiteIds();

        $oldWebsiteIds = [];

        $designidea->setIsChangedWebsites(false);

        $connection = $this->getConnection();

        $oldWebsiteIds = $this->getWebsiteIds($designidea);

        $insert = array_unique(array_diff($websiteIds, $oldWebsiteIds));
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $websiteId) {
                $data[] = ['designidea_id' => (int)$designidea->getEntityId(), 'website_id' => (int)$websiteId];
            }
            $connection->insertMultiple($this->getDesignideaWebsiteTable(), $data);
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = ['designidea_id = ?' => (int)$designidea->getEntityId(), 'website_id = ?' => (int)$websiteId];

                $connection->delete($this->getDesignideaWebsiteTable(), $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $designidea->setIsChangedWebsites(true);
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
     * Retrieve template related products
     * @return \Magento\Catalog\Model\ResourceModel\Upsell\CollectionFactory
     */
    public function getUpsellDesignIdeas()
    {
        $collection = $this->_upsellCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_designidea_upsell_designidea')],
            'e.entity_id = rl.upsell_id',
            ['position']
        )->where(
            'rl.template_id = ?',
            $this->getId()
        );
        return $collection;
    }

    /**
     * Retrieve product website identifiers
     *
     * @param \Designnbuy\Designidea\Model\Designidea|int $designidea
     * @return array
     */
    public function getWebsiteIds($designidea)
    {
        $connection = $this->getConnection();

        if ($designidea instanceof \Designnbuy\Designidea\Model\Designidea) {
            $designideaId = $designidea->getEntityId();
        } else {
            $designideaId = $designidea;
        }

        $select = $connection->select()->from(
            $this->getDesignideaWebsiteTable(),
            'website_id'
        )->where(
            'designidea_id = ?',
            (int)$designideaId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Product Website table name getter
     *
     * @return string
     */
    public function getDesignideaWebsiteTable()
    {
        if (!$this->_designideaWebsiteTable) {
            $this->_designideaWebsiteTable = $this->getTable('designnbuy_designidea_website');
        }
        return $this->_designideaWebsiteTable;
    }

    /**
     *  Check whether designer identifier is numeric
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isNumericPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^[0-9]+$/', $object->getData('identifier'));
    }

     /**
     *  Check whether designer identifier is valid
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return bool
     */
    protected function isValidPageIdentifier(\Magento\Framework\Model\AbstractModel $object)
    {
        return preg_match('/^([^?#<>@!&*()$%^\\/+=,{}]+)?$/', $object->getData('identifier'));
    }
    
    ##Add checkIdentifier For Design Route By @AH
    /**
     * Check if designer identifier exist for specific store
     * return designer id if designer exists
     *
     * @param string $identifier
     * @param int|array $storeId
     * @return int
     */
    public function checkIdentifier($identifier, $storeIds)
    {
        if (!is_array($storeIds)) {
            $storeIds = [$storeIds];
        }
        $storeIds[] = \Magento\Store\Model\Store::DEFAULT_STORE_ID;
        $select = $this->_getLoadByIdentifierSelect($identifier, $storeIds);
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('ddev.entity_id')->order('ddev.entity_id DESC')->limit(1);
        return $this->getConnection()->fetchOne($select);
    }

    /**
     * Check if designer identifier exist for specific store
     * return designer id if designer exists
     *
     * @param string $identifier
     * @param int $storeId
     * @return int
     */
    protected function _getLoadByIdentifierSelect($identifier, $storeIds)
    {
        $select = $this->getConnection()->select()->from(
            ['ddev' => $this->getTable('designnbuy_designidea_entity_varchar')]
        )->where(
            'ddev.value = ?',
            $identifier
        );
        return $select;
    }
    //End @AH
}