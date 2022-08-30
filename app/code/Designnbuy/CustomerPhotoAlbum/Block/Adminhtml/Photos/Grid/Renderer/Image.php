<?php

namespace Designnbuy\CustomerPhotoAlbum\Block\Adminhtml\Photos\Grid\Renderer;

use Magento\Framework\DataObject;

class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $helper;
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Designnbuy\CustomerPhotoAlbum\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
        if($row->getPath()){
            return '<image height="50" src ="' . $this->helper->getAdminImageUrl($row->getPath()) . '" alt="Thumbnil Image" >';
        }

        return '';
    }
}