<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Controller\Product;

use Magento\Framework\Controller\ResultFactory;

/**
 * Product Service
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Canvas factory
     *
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    protected $_canvas;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Canvas\Model\Canvas $canvas
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_canvas = $canvas;
    }
    /**
     * category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
     
    public function execute()
    {
        $productId = $this->getRequest()->getParam('pid');
        try {
            $result = $this->_canvas->getProductData($productId, $area = 'admin');

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }
}


