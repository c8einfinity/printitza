<?php

namespace Unific\Connector\Helper;

use Magento\Store\Model\ScopeInterface;

class Hmac extends \Magento\Framework\App\Helper\AbstractHelper
{
    public function generateHmac(array $data)
    {
        return hash_hmac(
            'sha256',
            json_encode($data),
            $this->scopeConfig->getValue('unific/hmac/hmacSecret', ScopeInterface::SCOPE_STORE)
        );
    }

    /**
     * @return string
     */
    public function generateSecret()
    {
        return hash('MD5', uniqid(rand(), true));
    }
}
