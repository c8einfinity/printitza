<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Ui\DataProvider\Category\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Designidea\Model\ResourceModel\Category\Collection;
use Designnbuy\Designidea\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class CategoryDataProvider
 */
class CategoryDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var category
     */
    private $category;

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

        if (!$this->getCategory()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getCategory()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve category
     *
     * @return CategoryInterface|null
     */
    protected function getCategory()
    {
        if (null !== $this->category) {
            return $this->category;
        }

        if (!($id = $this->request->getParam('current_category_id'))) {
            return null;
        }

        return $this->category = $this->categoryRepository->getById($id);
    }
}
