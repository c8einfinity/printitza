<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Ui\DataProvider\Product\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Background\Model\ResourceModel\Background\Collection;
use Designnbuy\Background\Model\ResourceModel\Background\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class BackgroundDataProvider
 */
class BackgroundDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var background
     */
    private $background;

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

        if (!$this->getBackground()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getBackground()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve background
     *
     * @return BackgroundInterface|null
     */
    protected function getBackground()
    {
        if (null !== $this->background) {
            return $this->background;
        }

        if (!($id = $this->request->getParam('current_background_id'))) {
            return null;
        }

        return $this->background = $this->backgroundRepository->getById($id);
    }
}
