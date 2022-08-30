<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Merchandise\Controller\Adminhtml\Side\ConfigArea;

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
                'height' => $data['height'],
                'output_width' => $data['output_width'],
                'output_height' => $data['output_height'],
            ];
            $productId = $data['product_id'];
            $configareaId = $data['configarea_id'];
            $sideId = $data['side_id'];
            //json_encode($area);

            $areaCollection = $this->_configAreaCollectionFactory->create();
            $areaCollection->addFieldToFilter('configarea_id', $configareaId);
            $areaCollection->addFieldToFilter('product_id', $productId);
            $areaCollection->addFieldToFilter('side_id', $sideId);
            $configAreaItem = $areaCollection->getFirstItem();

            $configArea = $this->_configAreaFactory->create();
            if ($configAreaItem->getAreaId()) {
                $configArea->load($configAreaItem->getAreaId());
            }
            $configArea->setSideId($sideId);
            $configArea->setConfigareaId($configareaId);
            $configArea->setProductId($productId);
            $configArea->setArea(json_encode($area));
            $configArea->save();
            $result = ['message' => 'Design area has been saved.'];
        } catch (\Exception $e) {
            $result = ['message' => 'Something went wrong while saving design area.', 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }
}
