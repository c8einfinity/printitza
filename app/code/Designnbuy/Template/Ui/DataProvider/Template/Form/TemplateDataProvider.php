<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Template\Ui\DataProvider\Template\Form;

use Designnbuy\Template\Model\ResourceModel\Template\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for product edit form
 */
class TemplateDataProvider extends AbstractDataProvider
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

        /*$template = $this->registry->registry('current_template');

        if ($template) {
            $templateData = $template->getData();
            if (isset($templateData['image'])) {
                unset($templateData['image']);
                $templateData['image'][0]['name'] = 'Tulips.jpg';
                $templateData['image'][0]['url'] = 'http://192.168.0.75/dnb_products/AIODV30/pub/media/designnbuy/template/Tulips.jpg';
            }
        }
        $this->data['template'][$template->getId()] = $templateData;*/

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
