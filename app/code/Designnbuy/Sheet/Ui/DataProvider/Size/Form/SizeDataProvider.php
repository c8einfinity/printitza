<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */
namespace Designnbuy\Sheet\Ui\DataProvider\Size\Form;

use Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;

/**
 * Class DataProvider
 */
class SizeDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Designnbuy\Sheet\Model\ResourceModel\Size\Collection
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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $sizeCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $sizeCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $sizeCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->meta = $this->prepareMeta($this->meta);
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
        /** @var $size \Designnbuy\Sheet\Model\Size */
        foreach ($items as $size) {
            $size = $size->load($size->getId()); //temporary fix
            $data = $size->getData();

            /* Prepare Featured Image */
            $map = [
                'featured_img' => 'getFeaturedImage',
                'og_img' => 'getOgImage'
            ];
            foreach ($map as $key => $method) {
                if (isset($data[$key])) {
                    $name = $data[$key];
                    unset($data[$key]);
                    $data[$key][0] = [
                        'name' => $name,
                        'url' => $size->$method(),
                    ];
                }
            }

            $data['data'] = ['links' => []];

            /* Prepare related sizes */
            $collection = $size->getRelatedSizes();
            $items = [];
            foreach ($collection as $item) {
                    $itemData = $item->getData();
                    $itemData['id'] = $item->getId();
                    /* Fix for big request data array */
                    foreach (['content', 'short_content', 'meta_description'] as $field) {
                        if (isset($itemData[$field])) {
                            unset($itemData[$field]);
                        }
                    }
                    /* End */
                    $items[] = $itemData;
            }
            $data['data']['links']['size'] = $items;

            /* Prepare related products */
            $collection = $size->getRelatedProducts()->addAttributeToSelect('name');
            $items = [];
            foreach ($collection as $item) {
                $itemData = $item->getData();
                $itemData['id'] = $item->getId();
                /* Fix for big request data array */
                foreach (['description', 'short_description', 'meta_description'] as $field) {
                    if (isset($itemData[$field])) {
                        unset($itemData[$field]);
                    }
                }
                /* End */

                $items[] = $itemData;
            }
            $data['data']['links']['product'] = $items;

            /* Set data */
            $this->loadedData[$size->getId()] = $data;
        }

        $data = $this->dataPersistor->get('sheet_size_form_data');
        if (!empty($data)) {
            $size = $this->collection->getNewEmptyItem();
            $size->setData($data);
            $this->loadedData[$size->getId()] = $size->getData();
            $this->dataPersistor->clear('sheet_size_form_data');
        }

        return $this->loadedData;
    }
}
