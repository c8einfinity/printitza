<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Designidea\Controller\Category;

use Magento\Framework\Controller\ResultFactory;

/**
 * Clipart Category Service
 */
class CategoryList extends \Designnbuy\Clipart\App\Action\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Designnbuy\Clipart\Helper\Data
     */
    protected $_categoryHelper;

    /**
     * @var \Designnbuy\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Designidea\Helper\Data $categoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_categoryHelper = $categoryHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * View Font font action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('pid');
        try {
            $result = $this->_categoryHelper->getCategories($productId);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

    }
}
