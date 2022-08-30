<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Controller\Category;

/**
 * Background category view
 */
class View extends \Designnbuy\Background\App\Action\Action
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
    }

    /**
     * View background category action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->moduleEnabled()) {
            return $this->_forwardNoroute();
        }

        $category = $this->_initCategory();
        if (!$category) {
            return $this->_forwardNoroute();
        }

        $this->_objectManager->get('\Magento\Framework\Registry')
            ->register('current_background_category', $category);

        $resultPage = $this->_objectManager->get('Designnbuy\Background\Helper\Page')
            ->prepareResultPage($this, $category);
        return $resultPage;
    }

    /**
     * Init category
     *
     * @return \Designnbuy\Background\Model\category || false
     */
    protected function _initCategory()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $category = $this->_objectManager->create('Designnbuy\Background\Model\Category')->load($id);

        if (!$category->isVisibleOnStore($storeId)) {
            return false;
        }

        $category->setStoreId($storeId);

        return $category;
    }
}
