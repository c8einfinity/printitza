<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\PrintingMethod\Ui\DataProvider\PrintingMethod\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\Collection;
use Designnbuy\PrintingMethod\Model\ResourceModel\PrintingMethod\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class PrintingMethodDataProvider
 */
class PrintingMethodDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var printingmethod
     */
    private $printingmethod;

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

        if (!$this->getPrintingMethod()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getPrintingMethod()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve printingmethod
     *
     * @return PrintingMethodInterface|null
     */
    protected function getPrintingMethod()
    {
        if (null !== $this->printingmethod) {
            return $this->printingmethod;
        }

        if (!($id = $this->request->getParam('current_printingmethod_id'))) {
            return null;
        }

        return $this->printingmethod = $this->printingmethodRepository->getById($id);
    }
}
