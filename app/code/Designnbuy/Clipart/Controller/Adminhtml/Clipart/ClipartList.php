<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Clipart\Controller\Adminhtml\Clipart;

use Magento\Framework\Controller\ResultFactory;

/**
 * Clipart Service
 */
class ClipartList extends \Magento\Backend\App\Action
{

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Designnbuy\Clipart\Model\Clipart
     */
    protected $clipart;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Clipart\Model\Clipart $clipart,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_clipart = $clipart;
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
        $productId = $this->getRequest()->getParam('pid');
        $query = $this->getRequest()->getParam('q');
        try {
            $result = $this->_clipart->getCategoryRelatedCliparts($categoryId, $productId, $query);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);

    }
}
