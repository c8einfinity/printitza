<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Controller\Category;
use Magento\Framework\Controller\ResultFactory;
/**
 * Background home page view
 */
class Index extends \Designnbuy\Background\App\Action\Action
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
    protected $_category;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Background\Model\Category $category
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_category = $category;
    }
    /**
     * category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('id');
        try {
            $result = $this->_category->getBackgroundCategories($productId);
            //$result = $this->_category->getCategoryTree();

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
