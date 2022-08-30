<?php

namespace Designnbuy\Designidea\Block\Adminhtml\Designidea\Grid\Renderer;

use Magento\Framework\DataObject;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
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
        if($this->getColumn()->getIndex() == 'image' && $row->getImage()){
            return '<image height="50" src ="' . $row->getImage() . '" alt="' . $row->getImage() . '" >';
        } else  if($this->getColumn()->getIndex() == 'preview_image' && $row->getPreviewImage()){
            return '<image height="50" src ="' . $row->getPreviewImage() . '" alt="' . $row->getPreviewImage() . '" >';
        }
        return '';
        /*if($row->getImage()){

        }*/


    }
}