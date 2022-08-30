<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Designidea\App\Action;

/**
 * Designidea frontend action controller
 */
abstract class Action extends \Magento\Framework\App\Action\Action
{
    /**
     * Retrieve true if designidea extension is enabled.
     *
     * @return bool
     */
    protected function moduleEnabled()
    {
        return (bool) $this->getConfigValue(
            \Designnbuy\Designidea\Helper\Config::XML_PATH_EXTENSION_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Retrieve store config value
     *
     * @return string | null | bool
     */
    protected function getConfigValue($path)
    {
        $config = $this->_objectManager->get('\Magento\Framework\App\Config\ScopeConfigInterface');
        return $config->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Throw control to cms_index_noroute action.
     *
     * @return void
     */
    protected function _forwardNoroute()
    {
        $this->_forward('index', 'noroute', 'cms');
    }

}
