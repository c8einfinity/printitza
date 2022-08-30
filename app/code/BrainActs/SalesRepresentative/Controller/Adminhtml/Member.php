<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml;

use Magento\Backend\App\Action;

/**
 * Class Member
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
abstract class Member extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member_save';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    private $dateTimeFilter;

    /*
     * Initialize requested member and put it into registry.
     */

    protected function _initMember($readOnly = false)
    {

        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('member_id');
        $model = $this->_objectManager->create('BrainActs\SalesRepresentative\Model\Member');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This member no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
            $model->setProductsReadonly($readOnly);
        }

        $this->_objectManager->get('Magento\Framework\Registry')->register('salesrep_member', $model);

        return $model;
    }

    /**
     * Build response for ajax request
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Framework\Controller\Result\Json
     *
     * @deprecated
     */
    protected function ajaxRequestResponse($category, $resultPage)
    {
        // prepare breadcrumbs of selected category, if any
        $breadcrumbsPath = $category->getPath();
        if (empty($breadcrumbsPath)) {
            // but if no category, and it is deleted - prepare breadcrumbs from path, saved in session
            $breadcrumbsPath = $this->_objectManager->get(
                'Magento\Backend\Model\Auth\Session'
            )->getDeletedPath(
                true
            );
            if (!empty($breadcrumbsPath)) {
                $breadcrumbsPath = explode('/', $breadcrumbsPath);
                // no need to get parent breadcrumbs if deleting category level 1
                if (count($breadcrumbsPath) <= 1) {
                    $breadcrumbsPath = '';
                } else {
                    array_pop($breadcrumbsPath);
                    $breadcrumbsPath = implode('/', $breadcrumbsPath);
                }
            }
        }

        $eventResponse = new \Magento\Framework\DataObject([
            'content' => $resultPage->getLayout()->getUiComponent('category_form')->getFormHtml()
                . $resultPage->getLayout()->getBlock('category.tree')
                    ->getBreadcrumbsJavascript($breadcrumbsPath, 'editingCategoryBreadcrumbs'),
            'messages' => $resultPage->getLayout()->getMessagesBlock()->getGroupedHtml(),
            'toolbar' => $resultPage->getLayout()->getBlock('page.actions.toolbar')->toHtml()
        ]);
        $this->_eventManager->dispatch(
            'category_prepare_ajax_response',
            ['response' => $eventResponse, 'controller' => $this]
        );
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->_objectManager->get('Magento\Framework\Controller\Result\Json');
        $resultJson->setHeader('Content-type', 'application/json', true);
        $resultJson->setData($eventResponse->getData());
        return $resultJson;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     *
     * @deprecated
     */
    private function getDateTimeFilter()
    {
        if ($this->dateTimeFilter === null) {
            $this->dateTimeFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Stdlib\DateTime\Filter\DateTime::class);
        }
        return $this->dateTimeFilter;
    }

    /**
     * Datetime data pre-processing
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param array $postData
     *
     * @return array
     */
    protected function dateTimePreprocessing($category, $postData)
    {
        $dateFieldFilters = [];
        $attributes = $category->getAttributes();
        foreach ($attributes as $attrKey => $attribute) {
            if ($attribute->getBackend()->getType() == 'datetime') {
                if (array_key_exists($attrKey, $postData) && $postData[$attrKey] != '') {
                    $dateFieldFilters[$attrKey] = $this->getDateTimeFilter();
                }
            }
        }
        $inputFilter = new \Zend_Filter_Input($dateFieldFilters, [], $postData);
        return $inputFilter->getUnescaped();
    }
}
