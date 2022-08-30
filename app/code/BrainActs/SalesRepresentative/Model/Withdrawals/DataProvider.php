<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\Withdrawals;

use BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Withdrawals\Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var array
     */
    private $loadedData;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $withdrawalsCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $withdrawalsCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $withdrawalsCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        /** @var $withdrawal \BrainActs\SalesRepresentative\Model\Withdrawals */
        foreach ($items as $withdrawal) {
            $this->loadedData[$withdrawal->getId()] = $withdrawal->getData();
        }

        $data = $this->dataPersistor->get('salesrep_withdrawals');
        if (!empty($data)) {
            $withdrawal = $this->collection->getNewEmptyItem();
            $withdrawal->setData($data);
            $this->loadedData[$withdrawal->getId()] = $withdrawal->getData();
            $this->dataPersistor->clear('salesrep_withdrawals');
        }

        return $this->loadedData;
    }
}
