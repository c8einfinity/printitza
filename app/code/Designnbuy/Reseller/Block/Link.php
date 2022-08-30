<?php
namespace Designnbuy\Reseller\Block;

class Link extends \Magento\Framework\View\Element\Html\Link
{
    protected $_url;

    protected $_resellerHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magefan\Blog\Model\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Reseller\Model\Url $url,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\Customer\Model\Session $customerSession,
        array $data = []
    ) {
        $this->_url = $url;
        $this->_resellerHelper = $resellerHelper;
        $this->customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function isLoggedIn()
    {
        return $this->customerSession->isLoggedIn();
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->_url->getFormUrl();
    }

    protected function _toHtml()
    {
        $websiteId = $this->_storeManager->getWebsite()->getId();

        $resellerWebsite = $this->_resellerHelper->isResellerStore($websiteId);
        if ($resellerWebsite) {
            return '';
        }

        $logInStatus = $this->isLoggedIn();
         if(!$logInStatus):
            return parent::_toHtml();
         endif;
    }
}