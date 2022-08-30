<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Clipart\Ui\DataProvider\Clipart\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Clipart\Model\ResourceModel\Clipart\Collection;
use Designnbuy\Clipart\Model\ResourceModel\Clipart\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ClipartDataProvider
 */
class ClipartDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var clipart
     */
    private $clipart;

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

        if (!$this->getClipart()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getClipart()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve clipart
     *
     * @return ClipartInterface|null
     */
    protected function getClipart()
    {
        if (null !== $this->clipart) {
            return $this->clipart;
        }

        if (!($id = $this->request->getParam('current_clipart_id'))) {
            return null;
        }

        return $this->clipart = $this->clipartRepository->getById($id);
    }
}
