<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Designidea\Block;

/**
 * Class Link
 */
class Link extends \Magento\Framework\View\Element\Html\Link
{
    /**
     * @var \Designnbuy\Designidea\Model\Url
     */
    protected $_url;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Designnbuy\Designidea\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Designidea\Model\Url $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_url->getBaseUrl();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->_scopeConfig->getValue(
            'dnbdesignidea/index_page/title',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_scopeConfig->getValue(
            \Designnbuy\Designidea\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        )) {
            return '';
        }

        return parent::_toHtml();
    }
}
