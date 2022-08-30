<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Model\Member;

use BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member\Collection
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
     * Constructor
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $memberCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $memberCollectionFactory,
        DataPersistorInterface $dataPersistor,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $memberCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        /** @var \BrainActs\SalesRepresentative\Model\Member $member */
        foreach ($items as $member) {
            $this->loadedData[$member->getId()] = $member->getData();
        }

        $data = $this->dataPersistor->get('salesrep_member');
        if (!empty($data)) {
            $member = $this->collection->getNewEmptyItem();
            $member->setData($data);
            $this->loadedData[$member->getId()] = $member->getData();
            $this->dataPersistor->clear('member_id');
        }

        return $this->loadedData;
    }
}
