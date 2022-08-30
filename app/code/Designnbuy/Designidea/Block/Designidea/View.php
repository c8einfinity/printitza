<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Designidea\Block\Designidea;

use Magento\Store\Model\ScopeInterface;

/**
 * Designidea designidea view
 */
class View extends AbstractDesignidea
{
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $designidea = $this->getDesignidea();
        if ($designidea) {
            $this->_addBreadcrumbs($designidea->getTitle(), 'designidea_designidea');
            $this->pageConfig->addBodyClass('designidea-designidea-' . $designidea->getIdentifier());
            $this->pageConfig->getTitle()->set($designidea->getMetaTitle());
            $this->pageConfig->setKeywords($designidea->getMetaKeywords());
            $this->pageConfig->setDescription($designidea->getMetaDescription());
            $this->pageConfig->addRemotePageAsset(
                $designidea->getDesignideaUrl(),
                'canonical',
                ['attributes' => ['rel' => 'canonical']]
            );

            $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
            if ($pageMainTitle) {
                $pageMainTitle->setPageTitle(
                    $this->escapeHtml($designidea->getTitle())
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

            $designideaTitle = $this->_scopeConfig->getValue(
                'dnbdesignidea/index_page/title',
                ScopeInterface::SCOPE_STORE
            );
            $breadcrumbsBlock->addCrumb(
                'designidea',
                [
                    'label' => __($designideaTitle),
                    'title' => __($designideaTitle),
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
