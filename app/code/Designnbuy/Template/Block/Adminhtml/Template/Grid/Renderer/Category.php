<?php

namespace Designnbuy\Template\Block\Adminhtml\Template\Grid\Renderer;

use Magento\Framework\DataObject;

class Category extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var array
     */
    static protected $categories = [];
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Designnbuy\Template\Model\CategoryFactory $categoryFactory
    ) {
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if($row->getCategoryId()){
            $titles = [];
            $categoryIds = explode(',', $row->getCategoryId());

            
            foreach ($categoryIds as $id) {
                
                $title = $this->getCategoryById($id)->getTitle();
                if ($title) {
                    $titles[] = $title;
                }
            }

            return implode(', ', $titles);
        }

        return '';
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