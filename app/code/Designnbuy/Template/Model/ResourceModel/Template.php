<?php

namespace Designnbuy\Template\Model\ResourceModel;

use Magento\Catalog\Model\ResourceModel\AbstractResource;

/**
 * Template entity resource model
 *
 * @SuppressWarnings(PHPMD.LongVariable)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Template extends AbstractResource
{
    /**
     * Product to website linkage table
     *
     * @var string
     */
    protected $_templateWebsiteTable;
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
     * Template Product factory.
     *
     * @var \Designnbuy\Template\Model\Template\ProductFactory
     */
    public $_templateProductFactory;

    protected $_productInstance = null;

    /**
     * Template Upsell factory.
     *
     * @var \Designnbuy\Template\Model\Upsell\TemplateFactory
     */
    public $_templateUpsellFactory;

    /**
     * Template Crosssell factory.
     *
     * @var \Designnbuy\Template\Model\Crosssell\TemplateFactory
     */
    public $_templateCrosssellFactory;

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
        \Designnbuy\Template\Model\Template\Product $templateProductFactory,
        \Designnbuy\Template\Model\Template\Upsell $templateUpsellFactory,
        \Designnbuy\Template\Model\Template\Crosssell $templateCrosssellFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Designnbuy\Template\Model\ResourceModel\Template\Upsell\CollectionFactory $upsellCollectionFactory,
        \Designnbuy\Template\Model\ResourceModel\PageIdentifierGenerator $pageIdentifierGenerator,
        $data = []
    ) {
        $this->_storeManager = $storeManager;
        $this->_modelFactory = $modelFactory;
        $this->_templateProductFactory = $templateProductFactory;
        $this->_templateUpsellFactory = $templateUpsellFactory;
        $this->_templateCrosssellFactory = $templateCrosssellFactory;
        parent::__construct($context, $storeManager, $modelFactory, $data);
        $this->setType(\Designnbuy\Template\Model\Template::ENTITY);
        $this->connectionName  = 'template';
        $this->setConnection('template_read', 'template_write');
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_upsellCollectionFactory = $upsellCollectionFactory;
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
            $this->setType(\Designnbuy\Template\Model\Template::ENTITY);
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
        //return parent::_beforeSave($object);
        $typeId = $object->getTypeId();
        $object->setTypeId($typeId);
        $this->_saveCategoryIds($object);
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
     * @return Designnbuy\Template\Model\Template\Product
     * @author Ajay Makwana
     */
    public function getProductInstance()
    {
        if (!$this->_productInstance) {
            $this->_productInstance = $this->_templateProductFactory;
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
            $this->_upsellInstance = $this->_templateUpsellFactory;
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
            $this->_crosssellInstance = $this->_templateCrosssellFactory;
        }
        return $this->_crosssellInstance;
    }

    /**
     * save product template relation
     *
     * @access public
     * @return Designnbuy\Template\Model\Template
     * @author Ajay Makwana
     */
    /**
     * Get store ids to which specified item is assigned
     * @access public
     * @param int $templateId
     * @return array
     * @author Ajay Makwana
     */
    protected function _afterSave(\Magento\Framework\DataObject $object)
    {
        $this->_saveTypeId($object);
        $this->_saveWebsiteIds($object);
        $this->getProductInstance()->saveProductTemplateRelation($object);
        $this->getUpsellInstance()->saveUpsellTemplateRelation($object);
        $this->getCrosssellInstance()->saveCrosssellTemplateRelation($object);
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

    /**
     * Save Template website relations
     *
     * @param Designnbuy\Template\Model\Template $template
     * @return $this
     */
    protected function _saveWebsiteIds($template)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            $id = $this->_storeManager->getDefaultStoreView()->getWebsiteId();
            $template->setWebsiteIds([$id]);
        }
        $websiteIds = $template->getWebsiteIds();

        $oldWebsiteIds = [];

        $template->setIsChangedWebsites(false);

        $connection = $this->getConnection();

        $oldWebsiteIds = $this->getWebsiteIds($template);

        $insert = array_unique(array_diff($websiteIds, $oldWebsiteIds));
        $delete = array_diff($oldWebsiteIds, $websiteIds);

        if (!empty($insert)) {
            $data = [];
            foreach ($insert as $websiteId) {
                $data[] = ['template_id' => (int)$template->getEntityId(), 'website_id' => (int)$websiteId];
            }
            $connection->insertMultiple($this->getTemplateWebsiteTable(), $data);
        }

        if (!empty($delete)) {
            foreach ($delete as $websiteId) {
                $condition = ['template_id = ?' => (int)$template->getEntityId(), 'website_id = ?' => (int)$websiteId];

                $connection->delete($this->getTemplateWebsiteTable(), $condition);
            }
        }

        if (!empty($insert) || !empty($delete)) {
            $template->setIsChangedWebsites(true);
        }

        return $this;
    }

    /**
     * Retrieve template related products
     * @return \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public function getRelatedProducts()
    {
        $collection = $this->_productCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_template_relatedproduct')],
            'e.entity_id = rl.related_id',
            ['position']
        )->where(
            'rl.template_id = ?',
            $this->getId()
        );
        return $collection;
    }


    /**
     * Retrieve template related products
     * @return \Magento\Catalog\Model\ResourceModel\Upsell\CollectionFactory
     */
    public function getUpsellTemplates()
    {
        $collection = $this->_upsellCollectionFactory->create();
        $collection->getSelect()->joinLeft(
            ['rl' => $this->getTable('designnbuy_template_upsell_template')],
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
     * @param \Designnbuy\Template\Model\Template|int $template
     * @return array
     */
    public function getWebsiteIds($template)
    {
        $connection = $this->getConnection();

        if ($template instanceof \Designnbuy\Template\Model\Template) {
            $templateId = $template->getEntityId();
        } else {
            $templateId = $template;
        }

        $select = $connection->select()->from(
            $this->getTemplateWebsiteTable(),
            'website_id'
        )->where(
            'template_id = ?',
            (int)$templateId
        );

        return $connection->fetchCol($select);
    }

    /**
     * Product Website table name getter
     *
     * @return string
     */
    public function getTemplateWebsiteTable()
    {
        if (!$this->_templateWebsiteTable) {
            $this->_templateWebsiteTable = $this->getTable('designnbuy_template_website');
        }
        return $this->_templateWebsiteTable;
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
        $select->reset(\Zend_Db_Select::COLUMNS)->columns('dtev.entity_id')->order('dtev.entity_id DESC')->limit(1);
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
            ['dtev' => $this->getTable('designnbuy_template_entity_varchar')]
        )->where(
            'dtev.value = ?',
            $identifier
        );
        return $select;
    }
}