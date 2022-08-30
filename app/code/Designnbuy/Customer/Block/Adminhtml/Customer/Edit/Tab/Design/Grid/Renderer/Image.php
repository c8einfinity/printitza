<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Block\Adminhtml\Customer\Edit\Tab\Design\Grid\Renderer;

/**
 * Adminhtml newsletter queue grid block status item renderer
 */
class Image extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Registry $registry,
        \Designnbuy\Base\Helper\Output $outputHelper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_outputHelper = $outputHelper;
        parent::__construct($context, $data);
    }
    /**
     * @param \Magento\Framework\DataObject $row
     * @return \Magento\Framework\Phrase
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if($row->getPng()){

                $pngs = $row->getPng();
                $images = explode(',', $pngs);
                   $imageHTML = '';
                 foreach($images as $image):
                     if($this->getImageUrl($image)):
                         $imageHTML .= '<a href="#"  tabindex="-1" class="product-item-photo">
                                                    <span class="product-image-container" style="width:165px;">
                                                        <span class="product-image-wrapper" style="padding-bottom: 100%;">
                                                            <img class="product-image-photo " src="' . $this->getImageUrl($image) . '" alt="" width="165" height="165">
                                                        </span>
                                                    </span>
                        </a>';
                     endif;
                 endforeach;
            return $imageHTML;
        //            return '<image height="50" src ="' . $row->getImage() . '" alt="' . $row->getImage() . '" >';
        }

        return '';
    }

    public function getImageUrl($image)
    {
        if(file_exists($this->_outputHelper->getCustomerDesignsDir().$image)){
            return $this->_outputHelper->getCustomerDesignsUrl().$image;
        }
        return;
    }
}
