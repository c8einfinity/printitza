<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Ui\DataProvider\Category\Form;

use Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class CategoryDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Category\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;
    
    /**
     * @var \Designnbuy\Background\Model\Category\ValueProvider
     */
    protected $metadataValueProvider;
    
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $categoryCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $categoryCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Background\Model\Metadata\ValueProvider $metadataValueProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $categoryCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $meta = array_replace_recursive($this->getMetadataValues(), $meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
    }

    /**
     * Get metadata values
     *
     * @return array
     */
    protected function getMetadataValues()
    {
        $metaDataValues = array();
        $rule = $this->coreRegistry->registry('current_model');
        if($rule){
            $metaDataValues = $this->metadataValueProvider->getMetadataValues($rule);
        }
        return $metaDataValues;
    }
    
    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        /** @var $category \Designnbuy\Background\Model\Category */
        foreach ($items as $category) {
            $category = $category->load($category->getId()); //temporary fix
            $this->loadedData[$category->getId()] = $category->getData();
        }

        $data = $this->dataPersistor->get('current_model');
        if (!empty($data)) {
            $category = $this->collection->getNewEmptyItem();
            $category->setData($data);
            $this->loadedData[$category->getId()] = $category->getData();
            $this->dataPersistor->clear('current_model');
        }

        return $this->loadedData;
    }
}
