<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Template\Block\Template;

use Magento\Store\Model\ScopeInterface;

/**
 * Template template view
 */
class View extends AbstractTemplate
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $template = $this->getTemplate();
        if ($template) {
            $this->_addBreadcrumbs($template->getTitle(), 'template_template');
            $this->pageConfig->addBodyClass('template-template-' . $template->getIdentifier());
            $this->pageConfig->getTitle()->set($template->getMetaTitle());
            $this->pageConfig->setKeywords($template->getMetaKeywords());
            $this->pageConfig->setDescription($template->getMetaDescription());
            $this->pageConfig->addRemotePageAsset(
                $template->getTemplateUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    $this->escapeHtml($template->getTitle())
                );
            }
        }

        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs
     *
     * @param  string $title
     * @param  string $key
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _addBreadcrumbs($title = null, $key = null)
    {
        if ($this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE)
            && ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs'))
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );

            $templateTitle = $this->_scopeConfig->getValue(
                'dnbtemplate/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'template',
                [
                    'label' => __($templateTitle),
                    'title' => __($templateTitle),
                    'link' => $this->_url->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb($key, [
                'label' => $title ,
                'title' => $title
            ]);
        }
    }

}
