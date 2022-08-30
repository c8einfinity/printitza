<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Background\Controller\Background;

/**
 * Background background view
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
     * View Background background action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->moduleEnabled()) {
            return $this->_forwardNoroute();
        }

        $background = $this->_initBackground();
        if (!$background) {
            return $this->_forwardNoroute();
        }

        $this->_objectManager->get('\Magento\Framework\Registry')
            ->register('current_background_background', $background);
        $resultPage = $this->_objectManager->get('Designnbuy\Background\Helper\Page')
            ->prepareResultPage($this, $background);
        return $resultPage;
    }

    /**
     * Init Background
     *
     * @return \Designnbuy\Background\Model\Background || false
     */
    protected function _initBackground()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $background = $this->_objectManager->create('Designnbuy\Background\Model\Background')->load($id);

        if (!$background->isVisibleOnStore($storeId)) {
            return false;
        }

        $background->setStoreId($storeId);

        return $background;
    }

}
