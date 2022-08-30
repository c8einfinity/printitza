<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Font\Ui\DataProvider\Font\Related;

use \Magento\Ui\DataProvider\AbstractDataProvider;
use Designnbuy\Font\Model\ResourceModel\Font\Collection;
use Designnbuy\Font\Model\ResourceModel\Font\CollectionFactory;
use Magento\Framework\App\RequestInterface;

/**
 * Class FontDataProvider
 */
class FontDataProvider extends AbstractDataProvider
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var font
     */
    private $font;

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

        if (!$this->getFont()) {
            return $collection;
        }

        $collection->addFieldToFilter(
            $collection->getIdFieldName(),
            ['nin' => [$this->getFont()->getId()]]
        );

        return $this->addCollectionFilters($collection);
    }

    /**
     * Retrieve font
     *
     * @return FontInterface|null
     */
    protected function getFont()
    {
        if (null !== $this->font) {
            return $this->font;
        }

        if (!($id = $this->request->getParam('current_font_id'))) {
            return null;
        }

        return $this->font = $this->fontRepository->getById($id);
    }
}
