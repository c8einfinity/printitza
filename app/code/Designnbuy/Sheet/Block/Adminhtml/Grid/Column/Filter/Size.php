<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Grid\Column\Filter;

/**
 * Category grid filter
 */
class Size extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @var \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\DB\Helper $resourceHelper
     * @param \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\DB\Helper $resourceHelper,
        \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory $collectionFactory,
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
        $options[] = ['value' => '', 'label' => __('All Sizes')];
        foreach ($this->collectionFactory->create()->load() as $item) {
            $options[] = ['value' => $item->getWidth() .' X '. $item->getHeight(), 'label' => $item->getWidth() .' X '. $item->getHeight()];
        };
        return $options;
    }
}
