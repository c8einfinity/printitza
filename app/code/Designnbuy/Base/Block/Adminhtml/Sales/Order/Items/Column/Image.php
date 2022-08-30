<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Base\Block\Adminhtml\Sales\Order\Items\Column;



/**
 * Sales Order items name column renderer
 */
class Image extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @var \Designnbuy\HotFolder\Helper\Data
     */

    private $hotFolderHelper;


    private $dnbBaseHelper;

    private $_outputHelper;

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
        \Designnbuy\HotFolder\Helper\Data $hotFolderHelper,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->authSession = $authSession;
        $this->hotFolderHelper = $hotFolderHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
        $this->_outputHelper = $outputHelper;
        $this->_catalogProduct = $catalogProduct;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    public function getImage()
    {

        
        //$this->fileFactory->create('cliparts.csv', $content, DirectoryList::VAR_DIR);

        $itemOptions = $this->getItem()->getProductOptionByCode('info_buyRequest');
        if(isset($itemOptions['png']) && !empty($itemOptions['png'])){
            $pngs = explode(',', $itemOptions['png']);
            if(isset($pngs[0]) && !empty($pngs[0])){
                if(file_exists($this->_outputHelper->getCartDesignsDir().$pngs[0])){
                    return $this->_outputHelper->getCartDesignsUrl().$pngs[0];
                }
            }
        } else {
            return $this->_catalogProduct->getImageUrl($this->getItem()->getProduct());
        }

    }


}
