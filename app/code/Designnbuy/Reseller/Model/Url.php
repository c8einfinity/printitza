<?php
namespace Designnbuy\Reseller\Model;

class Url
{
    const CONTROLLER_PRODUCTPOOL = 'productpool';
    
    protected $urlBuilder;
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function getFormUrl()
    {
        return $this->urlBuilder->getUrl('reseller/account/register');
    }

    public function getRegisterPostUrl()
    {
        return $this->urlBuilder->getUrl('reseller/account/create');
    }
}