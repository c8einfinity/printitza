<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Template;

use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Designnbuy\Customer\Controller\Adminhtml\Template
{
    /**
     * Save Customer Template
     *
     * @return void
     */
    public function execute()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/template'));
        }
        $template = $this->_objectManager->create('Designnbuy\Customer\Model\Template');

        $id = (int)$request->getParam('id');
        if ($id) {
            $template->load($id);
        }

        try {
            $template->addData(
                $request->getParams()
            )->setTemplateSubject(
                $request->getParam('subject')
            )->setTemplateCode(
                $request->getParam('code')
            )->setTemplateSenderEmail(
                $request->getParam('sender_email')
            )->setTemplateSenderName(
                $request->getParam('sender_name')
            )->setTemplateText(
                $request->getParam('text')
            )->setTemplateStyles(
                $request->getParam('styles')
            )->setModifiedAt(
                $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\DateTime')->gmtDate()
            );

            if (!$template->getId()) {
                $template->setTemplateType(TemplateTypesInterface::TYPE_HTML);
            }
            if ($this->getRequest()->getParam('_change_type_flag')) {
                $template->setTemplateType(TemplateTypesInterface::TYPE_TEXT);
                $template->setTemplateStyles('');
            }
            if ($this->getRequest()->getParam('_save_as_flag')) {
                $template->setId(null);
            }

            $template->save();

            $this->messageManager->addSuccess(__('The customer template has been saved.'));
            $this->_getSession()->setFormData(false);
            $this->_getSession()->unsPreviewData();
            $this->_redirect('*/template');
            return;
        } catch (LocalizedException $e) {
            $this->messageManager->addError(nl2br($e->getMessage()));
            $this->_getSession()->setData('customer_template_form_data', $this->getRequest()->getParams());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving this template.'));
            $this->_getSession()->setData('customer_template_form_data', $this->getRequest()->getParams());
        }

        $this->_forward('new');
    }
}
