<?php
namespace Designnbuy\Productattach\Block;

/**
 * Class Attachment
 * @package Designnbuy\Productattach\Block
 */
class Attachment extends \Magento\Framework\View\Element\Template
{
    /**
     * Productattach collection
     *
     * @var \Designnbuy\Productattach\Model\ResourceModel\Productattach\Collection
     */
    private $productattachCollection = null;
    
    /**
     * Productattach factory
     *
     * @var \Designnbuy\Productattach\Model\ProductattachFactory
     */
    private $productattachCollectionFactory;

    /**
     * Fileicon factory
     *
     * @var \Designnbuy\Productattach\Model\FileiconFactory
     */
    private $fileiconCollectionFactory;
    
    /**
     * @var \Designnbuy\Productattach\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    private $httpContext;

    /**
     * Attachment constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Designnbuy\Productattach\Model\ResourceModel\Productattach\CollectionFactory $productattachCollectionFactory
     * @param \Designnbuy\Productattach\Model\ResourceModel\Fileicon\CollectionFactory $fileiconCollectionFactory
     * @param \Designnbuy\Productattach\Helper\Data $dataHelper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Designnbuy\Productattach\Model\ResourceModel\Productattach\CollectionFactory $productattachCollectionFactory,
        \Designnbuy\Productattach\Model\ResourceModel\Fileicon\CollectionFactory $fileiconCollectionFactory,
        \Designnbuy\Productattach\Helper\Data $dataHelper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        $this->customerSession =$customerSession;
        $this->productattachCollectionFactory = $productattachCollectionFactory;
        $this->fileiconCollectionFactory = $fileiconCollectionFactory;
        $this->dataHelper = $dataHelper;
        $this->scopeConfig = $context->getScopeConfig();
        $this->registry = $registry;
        $this->httpContext = $httpContext;
        parent::__construct(
            $context,
            $data
        );
    }
    
    /**
     * Check module is enable or not
     */
    public function isEnable()
    {
        return $this->getConfig('productattach/general/enable');
    }

    /**
     * Retrieve productattach collection
     *
     * @return \Designnbuy\Productattach\Model\ResourceModel\Productattach\Collection
     */
    public function getCollection()
    {
        $collection = $this->productattachCollectionFactory->create();
        return $collection;
    }

    /**
     * Filter productattach collection by product Id
     *
     * @param $productId
     * @return \Designnbuy\Productattach\Model\ResourceModel\Productattach\Collection
     */
    public function getAttachment($productId)
    {
        $collection = $this->getCollection();
        
        $collection->addFieldToFilter(
            'customer_group',
            [
                ['null' => true],
                ['finset' => $this->getCustomerId()]
            ]
        );
        $collection->addFieldToFilter(
            'store',
            [
                ['eq' => 0],
                ['finset' => $this->dataHelper->getStoreId()]
            ]
        );

        $collection->getSelect()->where("products REGEXP '".$productId."'");
        
        return $collection;
    }

    /**
     * Retrive attachment url by attachment
     *
     * @return string
     */
    public function getAttachmentUrl($attachment)
    {
        $url = $this->dataHelper->getBaseUrl().$attachment;
        return $url;
    }

    /**
     * Retrive current product id
     *
     * @return number
     */
    public function getCurrentId()
    {
        $product = $this->registry->registry('current_product');
        return $product->getId();
    }

    /**
     * Retrive current customer id
     *
     * @return number
     */
    public function getCustomerId()
    {
        $isLoggedIn = $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if(!$isLoggedIn) {
            return 0;
        }

        $customerId = $this->customerSession->getCustomer()->getGroupId();
        return $customerId;
    }

    /**
     * Retrieve file icon image
     *
     * @param string $fileExt
     * @return string
     */
    public function getFileIcon($fileExt)
    {
        $fileExt = \strtolower($fileExt);

        if ($fileExt) {
            $iconExt = $this->getIconExt($fileExt);
            if ($iconExt) {
                $mediaUrl = $this->dataHelper->getMediaUrl();
                $iconImage = $mediaUrl.'fileicon/tmp/icon/'.$iconExt;
            } else {
                $iconImage = $this->getViewFileUrl('Designnbuy_Productattach::images/'.$fileExt.'.png');
            }
        } else {
            $iconImage = $this->getViewFileUrl('Designnbuy_Productattach::images/unknown.png');
        }
        return $iconImage;
    }

    /**
     * Retrive icon ext name
     *
     * @return string
     */
    public function getIconExt($fileExt)
    {
        $iconCollection = $this->fileiconCollectionFactory->create();
        $iconCollection->addFieldToFilter('icon_ext',$fileExt);
        $icon = $iconCollection->getFirstItem()->getIconImage();
        return $icon;
    }

    /**
     * Retrive link icon image
     *
     * @return string
     */
    public function getLinkIcon()
    {
        $iconImage = $this->getViewFileUrl('Designnbuy_Productattach::images/link.png');
        return $iconImage;
    }

    /**
     * Retrive file size by attachment
     *
     * @return number
     */
    public function getFileSize($attachment)
    {
        $attachmentPath = \Designnbuy\Productattach\Helper\Data::MEDIA_PATH.$attachment;
        $fileSize = $this->dataHelper->getFileSize($attachmentPath);
        return $fileSize;
    }

    /**
     * Retrive config value
     */
    public function getConfig($config)
    {
        return $this->scopeConfig->getValue(
            $config,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrive Tab Name
     */
    public function getTabName()
    {
        $tabName = __($this->getConfig('productattach/general/tabname'));
        return $tabName;
    }
}
