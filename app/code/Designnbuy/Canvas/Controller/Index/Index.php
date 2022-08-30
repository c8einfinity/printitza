<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */
namespace Designnbuy\Canvas\Controller\Index;

/**
 * Background home page view
 */
class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * View background homepage action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    /*public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }*/

    protected $_registry;
    protected $template;
    protected $pageFactory;
    protected $_translateInline;
    protected $_resultRawFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template $template,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline
    )
    {
        $this->_registry = $registry;
        $this->template = $template;
        $this->_resultRawFactory = $resultRawFactory;
        $this->_translateInline = $translateInline;
        return parent::__construct($context);
    }

    public function execute()
    {
        //$this->_view->loadLayout();
        //$this->_view->renderLayout();
        $html = $this->template->getLayout()->createBlock('Designnbuy\Canvas\Block\Index')->setIsCacheable(false)->setTemplate('index.phtml')->toHtml();
        $this->_translateInline->processResponseBody($html);

        $resultRaw = $this->_resultRawFactory->create();
        $resultRaw->setContents($html);
        return $resultRaw;
    }

}
