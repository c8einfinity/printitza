<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Merchandise\Controller\Adminhtml\Product;

use Magento\Framework\Controller\ResultFactory;

/**
 * Product Service
 */
class Index extends \Magento\Backend\App\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Catalog category factory
     *
     * @var \Designnbuy\Merchandise\Model\MerchandiseFactory
     */
    protected $_merchandise;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Merchandise\Model\Merchandise $merchandise
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_merchandise = $merchandise;
    }
    /**
     * category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();

        try {
            $result = $this->_merchandise->getProductData($request, 'admin');

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
