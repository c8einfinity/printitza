<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Controller\Adminhtml\Background;
use Magento\Framework\Controller\ResultFactory;
/**
 * Background home page view
 */
class BackgroundList extends \Magento\Backend\App\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Background factory
     *
     * @var \Designnbuy\Background\Model\Background
     */
    protected $_background;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Designnbuy\Background\Model\Background $background
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_background = $background;
    }
    /**
     * category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $productId = $this->getRequest()->getParam('id');
        $categoryId = $this->getRequest()->getParam('category');
        try {
            $result = $this->_background->getRelatedProductBackgrounds($productId, $categoryId);

        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

}
