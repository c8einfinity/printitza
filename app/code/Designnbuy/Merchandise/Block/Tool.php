<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Merchandise\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * Template index block
 */
class Tool extends \Magento\Framework\View\Element\Template
{
    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Canvas factory
     *
     * @var \Designnbuy\Canvas\Model\Canvas
     */
    protected $_canvas;

    /**
     * Template factory.
     *
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * Customer Design factory.
     *
     * @var \Designnbuy\Customer\Model\DesignFactory
     */
    protected $_designFactory;

    /**
     * @var \Magento\Base\Helper\Output
     */
    protected $_outputHelper;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Designnbuy\Base\Service\MobileDetect
     */
    protected $_mobileDetect;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Designnbuy\Base\Service\MobileDetect $mobileDetect
    ) {
        parent::__construct($context);
        $this->_mobileDetect = $mobileDetect;
        $this->setTemplatePHTML();
    }
    /**
     * Preparing global layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function setTemplatePHTML()
    {
        $template = 'Designnbuy_Merchandise::tool.phtml';
        if ($this->_mobileDetect->isMobile() || $this->_mobileDetect->isTablet()) {
            $template = 'Designnbuy_Merchandise::tool_mobile.phtml';
        }
        $this->setTemplate($template);
    }



}
