<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Threed\Controller\Adminhtml\ConfigArea;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\UrlInterface;
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Magento_Catalog::products';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;
    /**
     * @var \Designnbuy\Merchandise\Helper\Data
     */
    protected $_helper;
    /**
     * ConfigArea Factory.
     *
     * @var \Designnbuy\Merchandise\Model\ConfigAreaFactory
     */
    protected $_configAreaFactory;
    /**
     * ConfigArea Collection Factory.
     *
     * @var \Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory
     */
    protected $_configAreaCollectionFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Designnbuy\Merchandise\Helper\Data $helper,
        \Designnbuy\Merchandise\Model\ResourceModel\ConfigArea\CollectionFactory $_configAreaCollectionFactory,
        \Designnbuy\Merchandise\Model\ConfigAreaFactory $_configAreaFactory,
        UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->_helper = $helper;
        $this->_urlBuilder = $urlBuilder;
        $this->_configAreaFactory = $_configAreaFactory;
        $this->_configAreaCollectionFactory = $_configAreaCollectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $result = [];
        try {
            $data = $this->getRequest()->getParams();
            $area = [
                'pos_x' => $data['pos_x'],
                'pos_y' => $data['pos_y'],
                'width' => $data['width'],
                'height' => $data['height']
            ];

            $productId = $data['product_id'];
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $product = $objectManager->create('Magento\Catalog\Model\Product')->load($productId);
            $productAction = $objectManager->create(\Magento\Catalog\Model\Product\Action::class);
            $productAction->updateAttributes([$product->getId()], ['threed_configure_area' => json_encode($area)], 0);

            //$product->setThreedConfigureArea(json_encode($area));
            //$product->save();
            $result = ['message' => '3D Design area has been saved.'];
        } catch (\Exception $e) {
            //$result = ['message' => 'Something went wrong while saving design area.', 'errorcode' => $e->getCode()];
            $result = ['message' => 'Something went wrong while saving design area.', 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
