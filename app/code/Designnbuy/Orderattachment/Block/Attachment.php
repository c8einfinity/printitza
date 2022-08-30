<?php

namespace Designnbuy\Orderattachment\Block;

/**
 * Class Attachment
 * @package Designnbuy\Productattach\Block
 */
class Attachment extends \Magento\Framework\View\Element\Template
{

    protected $scopeConfig;

    protected $_helper;

    protected $_coreRegistry = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Checkout\Model\Cart $cart,
        \Designnbuy\Orderattachment\Helper\Data $orderAttchmentHelper,
        \Designnbuy\Orderattachment\Helper\Data $helper,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->cart = $cart;
        $this->_orderAttchmentHelper = $orderAttchmentHelper;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */

    protected function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getCurrentProcut() {
        return $this->_coreRegistry->registry('current_product');
    }

    public function getEnable() {
        $_product = $this->getCurrentProcut();
        $isEnabled = $this->_orderAttchmentHelper->getIsEnabled();
        $enabled = false;
        if($isEnabled && $_product->getAllowattachment() == 1){
            $enabled = true;
        }
        return $enabled;
    }

    public function getMaxFileSize()
    {
        $fileSize = $this->_orderAttchmentHelper->getMaxFileSize();
        $byteSize = 0;
        if($fileSize > 0){
            $byteSize = $fileSize * pow(1024,2);
        }
        return $byteSize;
    }

    public function getAllowedExtensions()
    {
        return $this->_orderAttchmentHelper->getAllowExtensionValue();
    }

    public function getAttachments()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $quoteItem = $this->cart->getQuote()->getItemById($id);
        $attachments = [];
        if($quoteItem) {
            $infoBuyRequest = $quoteItem->getOptionByCode('info_buyRequest');
            $itemOptions = (array) json_decode($infoBuyRequest->getValue());
            $jsonDecodeToClass = json_decode(json_encode($itemOptions), true);

            if(isset($jsonDecodeToClass['attachment']['fileName'])) {
                $cnt = 0;
                foreach ($jsonDecodeToClass['attachment']['fileName'] as $name) {
                    $attachments[] = [
                        'name' => $name,
                        'file' => $name,
                        'finalName' => $name,
                        'iconName' => $jsonDecodeToClass['attachment']['fileUrl'][$cnt],
                        'file_url' => $this->_orderAttchmentHelper->getImageUrlPath() . $name,
                        'preview_url' => $this->_orderAttchmentHelper->getImageUrlPath().$jsonDecodeToClass['attachment']['fileUrl'][$cnt],
                    ];
                    $cnt++;
                }

            }
        }
        return json_encode($attachments);
    }
}
