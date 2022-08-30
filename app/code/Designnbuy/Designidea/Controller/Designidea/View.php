<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Designidea\Controller\Designidea;

/**
 * Designidea Designidea view
 */
class View extends \Designnbuy\Designidea\App\Action\Action
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
     * View Designidea designidea action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->moduleEnabled()) {
            return $this->_forwardNoroute();
        }

        $designidea = $this->_initDesignidea();
        if (!$designidea) {
            return $this->_forwardNoroute();
        }

        $this->_objectManager->get('\Magento\Framework\Registry')
            ->register('current_designidea_designidea', $designidea);
        $resultPage = $this->_objectManager->get('Designnbuy\Designidea\Helper\Page')
            ->prepareResultPage($this, $designidea);
        return $resultPage;
    }

    /**
     * Init Designidea
     *
     * @return \Designnbuy\Designidea\Model\Designidea || false
     */
    protected function _initDesignidea()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $designidea = $this->_objectManager->create('Designnbuy\Designidea\Model\Designidea')->load($id);

        if (!$designidea->isVisibleOnStore($storeId)) {
            return false;
        }

        $designidea->setStoreId($storeId);

        return $designidea;
    }

}
