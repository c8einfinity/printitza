<?php
/**
 * Copyright © 2015-2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Background\Block\Adminhtml\Grid\Column\Filter;

/**
 * Category grid filter
 */
class Category extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Designnbuy\Background\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context, $resourceHelper, $data);
    }

    /**
     * @return array
     */
    protected function _getOptions()
    {
        $options = [];
        $options[] = ['value' => '', 'label' => __('All Categories')];
        foreach ($this->collectionFactory->create()->setOrder('title','ASC')->load() as $item) {
            $options[] = ['value' => $item->getId(), 'label' => $item->getTitle()];
        };
        return $options;
    }
}
