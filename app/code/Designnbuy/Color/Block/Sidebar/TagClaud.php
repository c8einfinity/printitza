<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Color\Block\Sidebar;

/**
 * Color tag claud sidebar block
 */
class TagClaud extends \Magento\Framework\View\Element\Template
{
    use Widget;

    /**
     * @var string
     */
    protected $_widgetKey = 'tag_claud';

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * @var \Designnbuy\Color\Model\ResourceModel\Tag\Collection
     */
    protected $_tags;

    /**
     * @var int
     */
    protected $_maxCount;

    /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory $_tagCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Color\Model\ResourceModel\Tag\CollectionFactory $tagCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_tagCollectionFactory = $tagCollectionFactory;
    }

    /**
     * Retrieve tags
     * @return array
     */
    public function getTags()
    {
        if ($this->_tags === null) {
            $this->_tags = $this->_tagCollectionFactory->create();
            $resource = $this->_tags->getResource();
            $this->_tags->getSelect()->joinLeft(
                ['pt' => $resource->getTable('designnbuy_color_color_tag')],
                'main_table.tag_id = pt.tag_id',
                []
            )->joinLeft(
                ['p' => $resource->getTable('designnbuy_color_color')],
                'p.color_id = pt.color_id',
                []
            )->joinLeft(
                ['ps' => $resource->getTable('designnbuy_color_color_store')],
                'p.color_id = ps.color_id',
                ['count' => 'count(main_table.tag_id)']
            )->group(
                'main_table.tag_id'
            )->where(
                'ps.store_id IN (?)',
                [0, (int)$this->_storeManager->getStore()->getId()]
            );
        }

        return $this->_tags;
    }

    /**
     * Retrieve max tag number
     * @return array
     */
    public function getMaxCount()
    {
        if ($this->_maxCount == null) {
            $this->_maxCount = 0;
            foreach ($this->getTags() as $tag) {
                $count = $tag->getCount();
                if ($count > $this->_maxCount) {
                    $this->_maxCount = $count;
                }
            }
        }
        return $this->_maxCount;
    }

    /**
     * Retrieve tag class
     * @return array
     */
    public function getTagClass($tag)
    {
        $maxCount = $this->getMaxCount();
        $percent = floor(($tag->getCount() / $maxCount) * 100);

        if ($percent < 20) {
            return 'smallest';
        }
        if ($percent >= 20 and $percent < 40) {
            return 'small';
        }
        if ($percent >= 40 and $percent < 60) {
            return 'medium';
        }
        if ($percent >= 60 and $percent < 80) {
            return 'large';
        }
        return 'largest';
    }

    /**
     * Retrieve block identities
     * @return array
     */
    public function getIdentities()
    {
        return [\Magento\Cms\Model\Block::CACHE_TAG . '_color_tag_claud_widget'  ];
    }

}
