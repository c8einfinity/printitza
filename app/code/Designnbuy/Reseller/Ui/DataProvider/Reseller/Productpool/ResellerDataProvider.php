<?php

namespace Designnbuy\Reseller\Ui\DataProvider\Reseller\Productpool;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Reseller\Model\ResourceModel\Productpool\Collection;
use Designnbuy\Reseller\Model\ResourceModel\Productpool\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class CategoryDataProvider
 */
class ResellerDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var template
     */
    private $template;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }
    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->getCollection();

        $arrItems = [
            'totalRecords' => $this->getCollection()->getSize(),
            'items' => [],
        ];

        foreach ($this->getCollection() as $item) {
            $arrItems['items'][] = $item->toArray([]);
        }

        return $arrItems;
    }
    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->setPageSize(50)->setCurPage(1);
        if (!$this->getTemplate()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getTemplate()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve productpool
     *
     * @return TemplateInterface|null
     */
    protected function getProductpool()
    {
        if (null !== $this->productpool) {
            return $this->productpool;
        }

        if (!($id = $this->request->getParam('current_productpool_id'))) {
            return null;
        }

        return $this->productpool = $this->productpoolRepository->getById($id);
    }
}
