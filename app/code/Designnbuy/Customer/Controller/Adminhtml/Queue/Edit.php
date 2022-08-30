<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Queue;

class Edit extends \Designnbuy\Customer\Controller\Adminhtml\Queue
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
    {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Edit Customer queue
     *
     * @return void
     */
    public function execute()
    {
        $this->_coreRegistry->register('current_queue', $this->_objectManager->get('Designnbuy\Customer\Model\Queue'));

        $id = $this->getRequest()->getParam('id');
        $templateId = $this->getRequest()->getParam('template_id');

        if ($id) {
            $queue = $this->_coreRegistry->registry('current_queue')->load($id);
        } elseif ($templateId) {
            $template = $this->_objectManager->create('Designnbuy\Customer\Model\Template')->load($templateId);
            $queue = $this->_coreRegistry->registry('current_queue')->setTemplateId($template->getId());
        }

        $this->_view->loadLayout();

        $this->_setActiveMenu('Designnbuy_Customer::designnbuy_customer_queue');
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Customer Queue'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Edit Queue'));

        $this->_addBreadcrumb(__('Customer Queue'), __('Customer Queue'), $this->getUrl('*/*'));
        $this->_addBreadcrumb(__('Edit Queue'), __('Edit Queue'));

        $this->_view->renderLayout();
    }
}
