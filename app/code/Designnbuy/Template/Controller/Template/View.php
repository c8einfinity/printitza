<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Controller\Template;

/**
 * Template Template view
 */
class View extends \Designnbuy\Template\App\Action\Action
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
     * View Template template action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->moduleEnabled()) {
            return $this->_forwardNoroute();
        }

        $template = $this->_initTemplate();
        if (!$template) {
            return $this->_forwardNoroute();
        }

        $this->_objectManager->get('\Magento\Framework\Registry')
            ->register('current_template_template', $template);
        $resultPage = $this->_objectManager->get('Designnbuy\Template\Helper\Page')
            ->prepareResultPage($this, $template);
        return $resultPage;
    }

    /**
     * Init Template
     *
     * @return \Designnbuy\Template\Model\Template || false
     */
    protected function _initTemplate()
    {
        $id = $this->getRequest()->getParam('id');
        $storeId = $this->_storeManager->getStore()->getId();

        $template = $this->_objectManager->create('Designnbuy\Template\Model\Template')->load($id);

        if (!$template->isVisibleOnStore($storeId)) {
            return false;
        }

        $template->setStoreId($storeId);

        return $template;
    }

}
