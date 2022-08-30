<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Index;

/**
 * Background home page view
 */
class GeneratePng extends \Magento\Framework\App\Action\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Data
     */
    protected $_baseHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Designnbuy\Base\Helper\Data $baseHelper
    ) {
        parent::__construct($context);
        $this->_baseHelper = $baseHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        echo $this->_baseHelper->getVarialbes();
        die;
    }

}
