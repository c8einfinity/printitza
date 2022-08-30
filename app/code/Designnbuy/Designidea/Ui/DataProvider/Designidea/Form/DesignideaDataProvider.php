<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Designidea\Ui\DataProvider\Designidea\Form;

use Designnbuy\Designidea\Model\ResourceModel\Designidea\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for product edit form
 */
class DesignideaDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        \Magento\Framework\Registry $registry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        /*$designidea = $this->registry->registry('current_designidea');

        if ($designidea) {
            $designIdeaData = $designidea->getData();
            if (isset($designIdeaData['image'])) {
                unset($designIdeaData['image']);
                $designIdeaData['image'][0]['name'] = 'Tulips.jpg';
                $designIdeaData['image'][0]['url'] = 'http://192.168.0.75/dnb_products/AIODV30/pub/media/designnbuy/designidea/Tulips.jpg';
            }
        }
        $this->data['designidea'][$designidea->getId()] = $designIdeaData;*/

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }

        return $meta;
    }
}
