<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Color\Ui\DataProvider\Color\Form;

use Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class ColorDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Color\Collection
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
     * @var \Designnbuy\Color\Model\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $colorCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $colorCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Color\Model\Metadata\ValueProvider $metadataValueProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $colorCollectionFactory->create();
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
        $rule = $this->coreRegistry->registry('current_model');
        return $this->metadataValueProvider->getMetadataValues($rule);
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
        /** @var $color \Designnbuy\Color\Model\Color */
        foreach ($items as $color) {
            $color = $color->load($color->getId()); //temporary fix
            $data = $color->getData();

            /* Prepare Image */
            $map = [
                'image' => 'getImage'
            ];
            foreach ($map as $key => $method) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $color->$method(),
                    ];
                }
            }

            $data['data'] = ['links' => []];

            /* Prepare related colors */
            $collection = $color->getRelatedColors();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                ];
            }
            $data['data']['links']['color'] = $items;

            /* Prepare related products */
            $collection = $color->getRelatedProducts()->addAttributeToSelect('name');
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }
            $data['data']['links']['product'] = $items;

            /* Set data */
            $this->loadedData[$color->getId()] = $data;
        }
        
        return $this->loadedData;
    }
}
