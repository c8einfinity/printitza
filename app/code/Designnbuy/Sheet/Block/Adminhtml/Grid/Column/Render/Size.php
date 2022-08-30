<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Block\Adminhtml\Grid\Column\Render;

/**
 * Category column renderer
 */
class Size extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Designnbuy\Sheet\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var array
     */
    static protected $categories = [];

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Designnbuy\Sheet\Model\CategoryFactory $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Designnbuy\Sheet\Model\SizeFactory $categoryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Render category grid column
     *
     * @param   \Magento\Framework\DataObject $row
     * @return  string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if ($row->getData('width') != '' && $row->getData('height') != '') {
            return $row->getData('width') .' X '. $row->getData('height');
        }
        return null;
    }
}
