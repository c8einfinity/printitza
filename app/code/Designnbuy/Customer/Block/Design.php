<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer subscribe block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\Customer\Block;

class Design extends \Magento\Framework\View\Element\Template
{

    const DELETE_URL = 'customer/design/delete';
    /**
     * @var string
     */
    //protected $_template = 'design.phtml';
    /**
     * Designs grid collection
     *
     * @var \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /** @var \Designnbuy\Customer\Model\ResourceModel\Design\Grid\Collection */
    protected $designs;

    protected $_productCollectionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory $collectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Customer\Model\ResourceModel\Design\Grid\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Designnbuy\Base\Helper\Output $outputHelper,
        \Magento\Catalog\Api\ProductRepositoryInterface $productCollectionFactory,
        array $data = []
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
        $this->_outputHelper = $outputHelper;
        $this->_productCollectionFactory = $productCollectionFactory;
    }

    /**
     * Initialize orderticket history content
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('design.phtml');
    }

    /*public function getDesigns()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->designs) {
            $this->designs = $this->_collectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $this->_customerSession->getCustomer()->getId()
            );
        }

        return $this->designs;
    }*/

    /**
     * Prepare orderticket returns history layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getDesigns()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'customer.designs.pager'
            )->setCollection(
                $this->getDesigns()
            );
            $this->setChild('pager', $pager);
            $this->getDesigns()->load();
        }
        return $this;
    }


    public function getDesigns()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->designs) {
            /** @var $returns \Designnbuy\Customer\Model\ResourceModel\Design\Grid\Collection */
            $this->designs = $this->_collectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $this->_customerSession->getCustomer()->getId()
            )->setOrder('design_id', 'asc');
        }

        return $this->designs;
    }

    /**
     * Get orderticket pager html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * Get orderticket view url
     *
     * @param \Magento\Framework\DataObject $return
     * @return string
     */
    public function getViewUrl($design)
    {
        if($design->getToolType() == 'producttool'){
            
            if($design->getProductId()){
                try {
                    $productCollection = $this->_productCollectionFactory->getById($design->getProductId());
                    if(!empty($productCollection->getData())){
                        $productUrl = $productCollection->getUrlKey();
                        $designid = $design->getDesignId();
                        $designName = str_replace(' ', '-', $design->getDesignName());
                        $designName = preg_replace('/[^A-Za-z0-9\-]/', '', $designName);
                        $url = $productUrl.'/'.$designid.'/'.$designName.'.html';
                        return rtrim($this->getUrl($url),'/');
                    }
                } catch (\Exception $e) {

                }
            }
                

            return $this->getUrl('merchandise/index/index', ['id' => $design->getProductId(), 'design_id' => $design->getDesignId()]);
        }

        if($design->getToolType() == 'web2print'){
            
            if($design->getProductId()){
                try {
                    $productCollection = $this->_productCollectionFactory->getById($design->getProductId());
                    if(!empty($productCollection->getData())){
                        $productUrl = $productCollection->getUrlKey();
                        $designid = $design->getDesignId();
                        $designName = str_replace(' ', '-', $design->getDesignName());
                        $designName = preg_replace('/[^A-Za-z0-9\-]/', '', $designName);
                        $url = $productUrl.'/'.$designid.'/'.$designName.'.html';
                        return rtrim($this->getUrl($url),'/');
                    }
                } catch (\Exception $e) {

                }
            }
            return $this->getUrl('canvas/index/index', ['id' => $design->getProductId(), 'design_id' => $design->getDesignId()]);
        }

    }

    /**
     * Get customer account back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }


    /**
     * Get post parameters for delete from cart
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return string
     */
    public function getDeletePostJson($item)
    {
        $url = $this->getUrl(self::DELETE_URL);

        $data = ['design_id' => $item->getDesignId()];

        return json_encode(['action' => $url, 'data' => $data]);
    }

    public function getImageUrl($image)
    {
        if(file_exists($this->_outputHelper->getCustomerDesignsDir().$image)){
            return $this->_outputHelper->getCustomerDesignsUrl().$image;
        }
        return;
    }
}
