<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\PrintingMethod\Ui\DataProvider\PrintingMethod\Form;

use Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
/**
 * Class DataProvider
 */
class PrintingMethodDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;
    /**
     * @var \Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection
     * @param PoolInterface $pool
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
     * @var \Designnbuy\PrintingMethod\Model\ValueProvider
     */
    protected $metadataValueProvider;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $printingmethodCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $printingmethodCollectionFactory,
        PoolInterface $pool,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        \Designnbuy\PrintingMethod\Model\Metadata\ValueProvider $metadataValueProvider,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $printingmethodCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $meta = array_replace_recursive($this->getMetadataValues(), $meta);
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
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
        /** @var $printingmethod \Designnbuy\PrintingMethod\Model\PrintingMethod */
        foreach ($items as $printingmethod) {
            $printingmethod = $printingmethod->load($printingmethod->getId()); //temporary fix
            $data = $printingmethod->getData();

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
                        'url' => $printingmethod->$method(),
                    ];
                }
            }

            $data['data'] = ['links' => []];


            /* Prepare related products */
            $collection = $printingmethod->getRelatedProducts()->addAttributeToSelect('name');
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                    'position' => $item->getPosition(),
                ];
            }
            $data['data']['links']['product'] = $items;
            $colors = [];
            $colors = $printingmethod->getRelatedColors();
            $data['color'] = $colors;

            $qaPrices = $printingmethod->getRelatedQAPrices();
            $data['qaprice'] = $qaPrices;

            $qcPrices = $printingmethod->getRelatedQCPrices();
            $data['qcprice'] = $qcPrices;

            $qPrices = $printingmethod->getRelatedQPrices();
            $data['qprice'] = $qPrices;

            /* Set data */
            $this->loadedData[$printingmethod->getId()] = $data;
        }
        
        return $this->loadedData;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
