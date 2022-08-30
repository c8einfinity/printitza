<?php
namespace Designnbuy\Customer\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Designnbuy\Productattach\Controller\Adminhtml\Index
 */
class Edit extends \Magento\Backend\App\Action
{
    private $resultForwardFactory;

    protected $_pageConfig;
    /**
     * @var  \Magento\Backend\Model\Session
     */
    private $backSession;

    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_pageConfig = $pageConfig;
        $this->backSession = $context->getSession();
        parent::__construct($context);
    }

    public function execute()
    {
        if($this->getRequest()->getParam('id') != "" && $this->getRequest()->getParam('id') == 0){
            $this->_pageConfig->addBodyClass('cust-group-not-loged-in');
        }
        return $this->resultForwardFactory->create()->forward('new');
    }
}
