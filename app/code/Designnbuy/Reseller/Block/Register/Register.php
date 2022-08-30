<?php
namespace Designnbuy\Reseller\Block\Register;

class Register extends \Magento\Framework\View\Element\Template
{
    protected $_url;
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Reseller\Model\Url $url,
        array $data = []
    ) {
        $this->_url = $url;
        parent::__construct(
            $context,
            $data
        );
    }

    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Create Reseller Account'));
        return parent::_prepareLayout();
    }

    public function getPostActionUrl()
    {
        return $this->_url->getRegisterPostUrl();
    }
}
