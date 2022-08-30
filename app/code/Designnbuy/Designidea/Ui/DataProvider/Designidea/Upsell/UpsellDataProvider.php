<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Ui\DataProvider\Designidea\Upsell;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell\Collection;
use Designnbuy\Designidea\Model\ResourceModel\Designidea\Upsell\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class CategoryDataProvider
 */
class UpsellDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var template
     */
    private $designidea;

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
        //$collection->setPageSize(50)->setCurPage(1);
        if (!$this->getDesignidea()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getDesignidea()->getId()]]
        );
        
        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve template
     *
     * @return DesignideaInterface|null
     */
    protected function getDesignidea()
    {
        if (null !== $this->designidea) {
            return $this->designidea;
        }

        if (!($id = $this->request->getParam('current_designidea_id'))) {
            return null;
        }

        return $this->designidea = $this->templateRepository->getById($id);
    }
}
