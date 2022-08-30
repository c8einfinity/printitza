<?php

namespace Unific\Connector\Helper;

use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\OrderRepository;
use Magento\Store\Model\App\Emulation;
use Unific\Connector\Model\HistoricalFactory;
use Unific\Connector\Model\ResourceModel\Historical\Collection as HistoricalCollection;
use Unific\Connector\Model\ResourceModel\Historical\CollectionFactory as HistoricalCollectionFactory;
use Unific\Connector\Model\ResourceModel\Queue\Collection as QueueCollection;
use Unific\Connector\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;

class Historical extends AbstractHelper
{
    /**
     * @var Message\Queue
     */
    protected $queueHelper;
    /**
     * @var QueueCollectionFactory
     */
    protected $queueCollectionFactory;
    /**
     * @var HistoricalCollectionFactory
     */
    protected $historicalCollectionFactory;
    /**
     * @var HistoricalFactory
     */
    protected $historicalFactory;
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var OrderRepository
     */
    protected $orderRepository;
    /**
     * @var CustomerRepository
     */
    protected $customerRepository;
    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;
    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Data\Order
     */
    protected $orderDataHelper;
    /**
     * @var Data\Customer
     */
    protected $customerDataHelper;
    /**
     * @var Data\Cart
     */
    protected $cartDataHelper;
    /**
     * @var Data\Product
     */
    protected $productDataHelper;
    /**
     * @var Data\Category
     */
    protected $categoryDataHelper;
    /**
     * @var Emulation
     */
    protected $emulation;

    /**
     * @param Context $context
     * @param QueueCollectionFactory $queueCollectionFactory
     * @param HistoricalCollectionFactory $historicalCollectionFactory
     * @param HistoricalFactory $historicalFactory
     * @param Message\Queue $queueHelper
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OrderRepository $orderRepository
     * @param CustomerRepository $customerRepository
     * @param ProductCollectionFactory $productCollectionFactory
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param Data\Order $orderDataHelper
     * @param Data\Cart $cartDataHelper
     * @param Data\Customer $customerDataHelper
     * @param Data\Product $productDataHelper
     * @param Data\Category $categoryDataHelper
     * @param Emulation $emulation
     */
    public function __construct(
        Context $context,
        QueueCollectionFactory $queueCollectionFactory,
        HistoricalCollectionFactory $historicalCollectionFactory,
        HistoricalFactory $historicalFactory,
        Message\Queue $queueHelper,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderRepository $orderRepository,
        CustomerRepository $customerRepository,
        ProductCollectionFactory $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        Data\Order $orderDataHelper,
        Data\Cart $cartDataHelper,
        Data\Customer $customerDataHelper,
        Data\Product $productDataHelper,
        Data\Category $categoryDataHelper,
        Emulation $emulation
    ) {
        parent::__construct($context);

        $this->queueHelper = $queueHelper;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->orderRepository = $orderRepository;
        $this->customerRepository = $customerRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->historicalCollectionFactory = $historicalCollectionFactory;
        $this->historicalFactory = $historicalFactory;
        $this->orderDataHelper = $orderDataHelper;
        $this->customerDataHelper = $customerDataHelper;
        $this->cartDataHelper = $cartDataHelper;
        $this->productDataHelper = $productDataHelper;
        $this->categoryDataHelper = $categoryDataHelper;
        $this->emulation = $emulation;
    }

    /**
     * Trigger the historical process
     *
     * @return array
     * @throws \Exception
     */
    public function triggerHistorical()
    {
        $this->resetAllHistoricalData();

        $this->triggerHistoricalForType('customer');
        $this->triggerHistoricalForType('order');
        $this->triggerHistoricalForType('category');
        $this->triggerHistoricalForType('product');

        return ['message' => 'Historical data has been triggered'];
    }

    /**
     * Trigger historical for a specific type
     *
     * @param $type
     * @param bool $removeOld
     *
     * @return array
     * @throws \Exception
     */
    public function triggerHistoricalForType($type, $removeOld = false)
    {
        if ($removeOld) {
            $this->removeHistoricalQueueType($type);
        }

        try {
            /** @var \Unific\Connector\Model\Historical $historical */
            $historical = $this->historicalFactory->create();
            $historical->setHistoricalType($type);
            $historical->setHistoricalTypePage(1);
            $historical->save();
        } catch (\Exception $e) {
            $this->_logger->critical($e);
        }

        return ['message' => 'Historical data for type [' . $type . '] has been triggered'];
    }

    /**
     * Delete a type from the historical queue process
     *
     * @param $type
     */
    public function removeHistoricalQueueType($type)
    {
        /** @var HistoricalCollection $collection */
        $collection = $this->historicalCollectionFactory->create();
        $collection->addFieldToFilter('historical_type', ['eq' => $type]);
        $collection->walk('delete');
    }

    /**
     * Inject historical data in the queue if historical is requested
     */
    public function queueHistorical()
    {
        /** @var HistoricalCollection $historicalCollection */
        $historicalCollection = $this->historicalCollectionFactory->create();

        if ($historicalCollection->getSize() === 0) {
            return;
        }

        foreach ($historicalCollection as $historicalItem) {
            try {
                $items = $this->processHistoricalItem($historicalItem);
                // Write back the latest ID to the historical queue manager
                if (count($items) == Settings::HISTORICAL_PAGE_SIZE) {
                    $this->incrementHistoricalQueuePage($historicalItem->getHistoricalType());
                } else {
                    $this->removeHistoricalQueueType($historicalItem->getHistoricalType());
                }
            } catch (\Exception $e) {
                $this->_logger->critical($e);
            }
        }
    }

    /**
     * Queue up all the historical data to be ready for sending
     */
    public function resetAllHistoricalData()
    {
        /** @var HistoricalCollection $collection */
        $collection = $this->historicalCollectionFactory->create();
        $collection->walk('delete');

        $this->resetHistoricalForType();

        return ['message' => 'Historical data has been stopped and reset'];
    }

    /**
     * Queue up all the historical data to be ready for sending
     * @param $type
     * @return array
     */
    public function resetHistoricalForType($type = null)
    {
        if ($type) {
            $this->removeHistoricalQueueType($type);
        }

        /** @var QueueCollection $queueCollection */
        $queueCollection = $this->queueCollectionFactory->create();
        $queueCollection->addFieldToFilter('headers', ['like' => '%' . $type . '/historical%']);

        $queueCollection->walk('delete');

        return ['message' => 'Historical data for type [' . $type . '] has been stopped and reset'];
    }

    /**
     * Update the latest queued ID
     *
     * @param $type
     */
    protected function incrementHistoricalQueuePage($type)
    {
        /** @var HistoricalCollection $collection */
        $collection = $this->historicalCollectionFactory->create();
        $collection->addFieldToFilter('historical_type', ['eq', $type]);

        foreach ($collection as $item) {
            $item->setHistoricalTypePage($item->getHistoricalTypePage() + 1);
        }

        $collection->save();
    }

    /**
     * @param $subject
     * @return array
     */
    protected function getHeaders($subject)
    {
        $headers = [];
        $headers['X-SUBJECT'] = $subject;

        return $headers;
    }

    /**
     * @param \Unific\Connector\Model\Historical $historicalItem
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function processHistoricalItem(\Unific\Connector\Model\Historical $historicalItem)
    {
        $items = $this->getTypeData($historicalItem, 'collection');

        $this->createQueueEntries(
            $items,
            $this->getTypeData($historicalItem, 'helper'),
            $historicalItem->getHistoricalType(),
            $this->getTypeData($historicalItem, 'priority')
        );

        return $items;
    }

    /**
     * @param iterable $items
     * @param $helper
     * @param string $type
     * @param int $priority
     */
    protected function createQueueEntries($items, $helper, $type, $priority)
    {
        $setEntityMethod = 'set' . ucwords($type);
        $getInfoMethod = 'get' . ucwords($type) . 'Info';
        foreach ($items as $item) {
            $helper->$setEntityMethod($item);
            if ($type === 'order') {
                if ($item->hasShipments()) {
                    $helper->setShipment($item->getShipmentsCollection()->getLastItem());
                }
                $this->emulation->startEnvironmentEmulation($item->getStoreId(), 'frontend');
            }

            $headers = $this->getHeaders($type . '/historical');

            $this->queueHelper->queue(
                $this->scopeConfig->getValue('unific/webhook/' . $type . '_endpoint'),
                $helper->$getInfoMethod(),
                $priority,
                $headers,
                \Zend\Http\Request::METHOD_POST,
                true,
                null,
                null,
                Settings::QUEUE_HISTORICAL_MAX_RETRIES
            );

            if ($type === 'order') {
                $this->emulation->stopEnvironmentEmulation();
            }
        }
    }

    /**
     * @param \Unific\Connector\Model\Historical $historical
     * @param string $element
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     * @todo if historical is set to save to file maybe increase Settings::HISTORICAL_PAGE_SIZE to make the job require less runs
     */
    protected function getTypeData(\Unific\Connector\Model\Historical $historical, string $element)
    {
        $helper = $element === 'helper';
        switch ($historical->getHistoricalType()) {
            case 'customer':
                if ($element === 'collection') {
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    $searchCriteria->setPageSize(Settings::HISTORICAL_PAGE_SIZE);
                    $searchCriteria->setCurrentPage($historical->getHistoricalTypePage());

                    return $this->customerRepository->getList($searchCriteria)->getItems();
                }
                return $helper ? $this->customerDataHelper : Settings::PRIORITY_CUSTOMER;
            case 'order':
                if ($element === 'collection') {
                    $searchCriteria = $this->searchCriteriaBuilder->create();
                    $searchCriteria->setPageSize(Settings::HISTORICAL_PAGE_SIZE);
                    $searchCriteria->setCurrentPage($historical->getHistoricalTypePage());

                    return $this->orderRepository->getList($searchCriteria)->getItems();
                }
                return $helper ? $this->orderDataHelper : Settings::PRIORITY_ORDER;
            case 'product':
                if ($element === 'collection') {
                    /** @var ProductCollection $items */
                    $items = $this->productCollectionFactory->create();
                    $items->addAttributeToSelect('*')
                        ->setPageSize(Settings::HISTORICAL_PAGE_SIZE)
                        ->setCurPage($historical->getHistoricalTypePage())
                        ->load()
                        ->addCategoryIds();

                    return $items;
                }
                return $helper ? $this->productDataHelper : Settings::PRIORITY_PRODUCT;
            case 'category':
                if ($element === 'collection') {
                    /** @var CategoryCollection $items */
                    $items = $this->categoryCollectionFactory->create();
                    $items->addAttributeToSelect('*')
                        ->setPageSize(Settings::HISTORICAL_PAGE_SIZE)
                        ->setCurPage($historical->getHistoricalTypePage());

                    return $items;
                }
                return $helper ? $this->categoryDataHelper : Settings::PRIORITY_CATEGORY;
        }

        throw new \InvalidArgumentException('Unknown historical type: ' . $historical->getHistoricalType());
    }
}
