<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Ui\DataProvider\Background\Form;

use Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class BackgroundDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Background\Collection
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
     * @var \Designnbuy\Background\Model\ValueProvider
     */
    protected $metadataValueProvider;

    protected $helper;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $backgroundCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $backgroundCollectionFactory,
        DataPersistorInterface $dataPersistor,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Background\Model\Metadata\ValueProvider $metadataValueProvider,
        \Designnbuy\Background\Helper\Data $helper,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $backgroundCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->coreRegistry = $registry;
        $this->metadataValueProvider = $metadataValueProvider;
        $this->helper = $helper;
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
        /** @var $background \Designnbuy\Background\Model\Background */
        foreach ($items as $background) {
            $background = $background->load($background->getId()); //temporary fix
            $data = $background->getData();

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
                        //'url' => $background->$method(),
                        'url' => $background->getImage(),
                        'size' => $this->helper->getFileSize(\Designnbuy\Background\Model\Background::BASE_MEDIA_PATH . '/' .$name),
                    ];
                }
            }

            $data['data'] = ['links' => []];

            /* Prepare related backgrounds */
            $collection = $background->getRelatedBackgrounds();
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'title' => $item->getTitle(),
                ];
            }
            $data['data']['links']['background'] = $items;

            /* Prepare related products */
            $collection = $background->getRelatedProducts()->addAttributeToSelect('name');
            $items = [];
            foreach($collection as $item) {
                $items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getName(),
                ];
            }
            $data['data']['links']['product'] = $items;
            $images = [];
            $images = $background->getBackgroundImages();
            $data['backgrounds'] = $images;

            /* Set data */
            $this->loadedData[$background->getId()] = $data;
        }

        return $this->loadedData;
    }
}
