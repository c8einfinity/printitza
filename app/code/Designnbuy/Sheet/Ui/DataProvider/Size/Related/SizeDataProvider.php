<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Ui\DataProvider\Size\Related;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Sheet\Model\ResourceModel\Size\Collection;
use Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class SizeDataProvider
 */
class SizeDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var size
     */
    private $size;

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
    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();

        if (!$this->getSize()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getSize()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve size
     *
     * @return SizeInterface|null
     */
    protected function getSize()
    {
        if (null !== $this->size) {
            return $this->size;
        }

        if (!($id = $this->request->getParam('current_size_id'))) {
            return null;
        }

        return $this->size = $this->sizeRepository->getById($id);
    }
}
