<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Ui\DataProvider\Color\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Color\Model\ResourceModel\Color\Collection;
use Designnbuy\Color\Model\ResourceModel\Color\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ColorDataProvider
 */
class ColorDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var color
     */
    private $color;

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

        if (!$this->getColor()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getColor()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve color
     *
     * @return ColorInterface|null
     */
    protected function getColor()
    {
        if (null !== $this->color) {
            return $this->color;
        }

        if (!($id = $this->request->getParam('current_color_id'))) {
            return null;
        }

        return $this->color = $this->colorRepository->getById($id);
    }
}
