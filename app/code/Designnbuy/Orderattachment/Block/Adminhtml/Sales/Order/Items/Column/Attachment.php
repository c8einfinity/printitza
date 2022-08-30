<?php

namespace Designnbuy\Orderattachment\Block\Adminhtml\Sales\Order\Items\Column;

/**
 * Sales Order items name column renderer
 */
class Attachment extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @var \Designnbuy\HotFolder\Helper\Data
     */

    private $dnbBaseHelper;

    /**
     * Catalog product
     *
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_catalogProduct;

    protected $authSession;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Designnbuy\Orderattachment\Helper\Data $attachmentHelper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->authSession = $authSession;
        $this->_attachmentHelper = $attachmentHelper;
        $this->_catalogProduct = $catalogProduct;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getAttachment()
    {
        
        $itemOptions = $this->getItem()->getProductOptionByCode('info_buyRequest');
        $html = '';
        if(isset($itemOptions) && $itemOptions != ''):
            $jsonDecodeToClass = json_decode(json_encode($itemOptions), true);
            if (isset($jsonDecodeToClass['attachment']['fileName']) && $jsonDecodeToClass['attachment']['fileName'] != '') {
                $cnt = 0;
                $html = '<div style="width:100px">';
                foreach ($jsonDecodeToClass['attachment']['fileName'] as $name) {
                        $html .= '<div style="padding-bottom:5px;">';
                        $html .= '<img src="'.$this->_attachmentHelper->getImageUrlPath().$jsonDecodeToClass['attachment']['fileUrl'][$cnt].'"  />';
                        $html .= '<a target="_blank" href="' . $this->_attachmentHelper->getImageUrlPath() . $name . '">';
                        $html .= $name;
                        $html .= '</a>';
                        $html .= '</div>';
                    $cnt++;
                }
                $html .= "</div>";
            }else{
                $html .= "<span>-</span>";    
            }           
        endif;
        return $html;

    }

    public function getAttachments()
    {
        $_item = $this->getItem();
        $productOptions = $_item->getBuyRequest()->getData();
        $itemAttachments = [];
        if(isset($productOptions['attachment']) && isset($productOptions)){
            $itemAttachments = $productOptions['attachment'];
        }
        return $itemAttachments;
    }

    public function getImageUrlPath()
    {
        return $this->_attachmentHelper->getImageUrlPath();
    }
}
