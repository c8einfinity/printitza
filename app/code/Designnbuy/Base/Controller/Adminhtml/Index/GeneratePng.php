<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Base\Controller\Adminhtml\Index;

/**
 * Background home page view
 */
class GeneratePng extends \Magento\Backend\App\Action
{
    /**
     * Output Helper Class
     *
     * @var \Designnbuy\Base\Helper\Output
     */
    protected $_outputHelper;
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Designnbuy\Base\Helper\Inkscape $inkscapeHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Base\Helper\Output $outputHelper
    ) {
        parent::__construct($context);
        $this->_outputHelper = $outputHelper;
    }
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $request = $this->getRequest()->getParams();
        echo $this->_outputHelper->generatePng($request);
        die;
    }

}
