<?php
/**
 * Copyright © 2015-2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Clipart\Block\Adminhtml\Grid\Column\Render;

/**
 * Category column renderer
 */
class Category extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Designnbuy\Clipart\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var array
     */	
    static protected $categories = [];

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Designnbuy\Clipart\Model\CategoryFactory $localeLists
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Designnbuy\Clipart\Model\CategoryFactory $categoryFactory,
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
        if ($data = $row->getData($this->getColumn()->getIndex())) {
            $titles = [];
            foreach ($data as $id) {
                $title = $this->getCategoryById($id)->getTitle();
                if ($title) {
                    $titles[] = $title;
                }
            }

            return implode(', ', $titles);
        }
        return null;
    }

    /**
     * Retrieve category by id
     *
     * @param   int $id
     * @return  \Designnbuy\Clipart\Model\Category
     */
    protected function getCategoryById($id)
    {
        if (!isset(self::$categories[$id])) {
            self::$categories[$id] = $this->categoryFactory->create()->load($id);
        }
        return self::$categories[$id];
    }
}
