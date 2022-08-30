<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\ConfigArea\Ui\DataProvider\ConfigArea\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea\Collection;
use Designnbuy\ConfigArea\Model\ResourceModel\ConfigArea\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class ConfigAreaDataProvider
 */
class ConfigAreaDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var configarea
     */
    private $configarea;

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

        if (!$this->getConfigArea()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getConfigArea()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve configarea
     *
     * @return ConfigAreaInterface|null
     */
    protected function getConfigArea()
    {
        if (null !== $this->configarea) {
            return $this->configarea;
        }

        if (!($id = $this->request->getParam('current_configarea_id'))) {
            return null;
        }

        return $this->configarea = $this->configareaRepository->getById($id);
    }
}
