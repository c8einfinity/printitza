<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model;

/**
 * Class OrderTicketRepository
 * Repository class for \Designnbuy\OrderTicket\Model\OrderTicket
 */
class OrderTicketRepository
{
    /**
     * orderticketFactory
     *
     * @var \Designnbuy\OrderTicket\Model\OrderTicketFactory
     */
    protected $orderticketFactory = null;

    /**
     * Collection Factory
     *
     * @var \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\CollectionFactory
     */
    protected $orderticketCollectionFactory = null;

    /**
     * Designnbuy\OrderTicket\Model\OrderTicket[]
     *
     * @var array
     */
    protected $registry = [];

    /**
     * Repository constructor
     *
     * @param \Designnbuy\OrderTicket\Model\OrderTicket $orderticketFactory
     * @param \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\CollectionFactory $orderticketCollectionFactory
     */
    public function __construct(
        \Designnbuy\OrderTicket\Model\OrderTicketFactory $orderticketFactory,
        \Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket\CollectionFactory $orderticketCollectionFactory
    ) {
        $this->orderticketFactory = $orderticketFactory;
        $this->orderticketCollectionFactory = $orderticketCollectionFactory;
    }

    /**
     * load entity
     *
     * @param int $id
     * @return \Designnbuy\OrderTicket\Model\OrderTicket
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id)
    {
        if (empty($id)) {
            throw new \Magento\Framework\Exception\InputException(__('ID cannot be an empty'));
        }
        if (!isset($this->registry[$id])) {
            $entity = $this->orderticketFactory->create()->load($id);
            if (!$entity->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Requested entity doesn\'t exist')
                );
            }
            $this->registry[$id] = $entity;
        }
        return $this->registry[$id];
    }

    /**
     * Register entity
     *
     * @param \Designnbuy\OrderTicket\Model\OrderTicket $object
     * @return \Designnbuy\OrderTicket\Model\OrderTicketRepository
     */
    public function register(\Designnbuy\OrderTicket\Model\OrderTicket $object)
    {
        if ($object->getId() && !isset($this->registry[$object->getId()])) {
            $object->load($object->getId());
            $this->registry[$object->getId()] = $object;
        }
        return $this;
    }

    /**
     * Find entities by criteria
     *
     * @param \Magento\Framework\Api\SearchCriteria $criteria
     * @return \Designnbuy\OrderTicket\Model\OrderTicket[]
     */
    public function find(\Magento\Framework\Api\SearchCriteria $criteria)
    {
        $collection = $this->orderticketCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $collection->addFieldToFilter($filter->getField(), [$condition => $filter->getValue()]);
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());
        foreach ($collection as $object) {
            $this->register($object);
        }
        $objectIds = $collection->getAllIds();
        return array_intersect_key($this->registry, array_flip($objectIds));
    }
}
