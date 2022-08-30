<?php

namespace Designnbuy\Pricecalculator\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

/**
 * Class Calculate
 * @package Designnbuy\Pricecalculator\Controller
 */
class Calculate extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;
    /**
     * Index constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute() {
        $params = $this->getRequest()->getPost();
        $calculation = $params['price'] * $params['height'] * $params['width'];
        echo $calculation;
    }
}
