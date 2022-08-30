<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Template\Ui\DataProvider\Template\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Template\Model\ResourceModel\Template\Collection;
use Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class CategoryDataProvider
 */
class TemplateDataProvider extends AbstractDataProvider
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
        //$collection->setPageSize(50)->setCurPage(1);
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
     * Retrieve template
     *
     * @return TemplateInterface|null
     */
    protected function getTemplate()
    {
        if (null !== $this->template) {
            return $this->template;
        }

        if (!($id = $this->request->getParam('current_template_id'))) {
            return null;
        }

        return $this->template = $this->templateRepository->getById($id);
    }
}
