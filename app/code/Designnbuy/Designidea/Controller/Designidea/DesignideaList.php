<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Designidea\Controller\Designidea;

use Magento\Framework\Controller\ResultFactory;

/**
 * Designidea Service
 */
class DesignideaList extends \Designnbuy\Designidea\App\Action\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Designnbuy\Designidea\Helper\Data
     */
    protected $_helper;

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
        $this->_helper = $categoryHelper;
        $this->_storeManager = $storeManager;
    }

    /**
     * View Font font action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $categoryId = $this->getRequest()->getParam('cat_id');
        try {
            $result = $this->_helper->getCategoryRelatedDesignIdeas($categoryId);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

    }
}
