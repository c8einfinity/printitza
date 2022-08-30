<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\Config\Source;

/**
 * Used in recent background widget
 *
 */
class Tag implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $tagCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Initialize dependencies.
     *
     * @param \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory $authorCollectionFactory
     * @param void
     */
    public function __construct(
        \Designnbuy\Background\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory
    ) {
        $this->tagCollectionFactory = $tagCollectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            $collection = $this->tagCollectionFactory->create();
            $collection->setOrder('title');

            foreach ($collection as $item) {
                $this->options[] = [
                    'label' => $item->getTitle(),
                    'value' => $item->getId(),
                ];
            }
        }

        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

}
