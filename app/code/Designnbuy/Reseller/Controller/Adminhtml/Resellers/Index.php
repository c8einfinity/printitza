<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

class Index extends \Magento\Backend\App\Action
{

    protected $coreRegistry;

    protected $requestRepository;

    protected $resultPageFactory;

    protected $authSession;

    protected $resultForwardFactory;

    protected $_resellerHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Designnbuy\Reseller\Helper\Data $resellerHelper
    ) {
        $this->coreRegistry      = $coreRegistry;
        $this->requestRepository = $requestRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->authSession       = $authSession;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_resellerHelper = $resellerHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $user = $this->authSession->getUser();

        $resellerId = $this->_resellerHelper->isResellerUser($user->getId());
        if($resellerId){
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/edit',array('reseller_id'=>$resellerId));
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Designnbuy_Base::network');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Reseller Websites'));
        $resultPage->addBreadcrumb(__('Manage Resellers'), __('Manage Resellers'));
        $resultPage->addBreadcrumb(__('Resellers'), __('Resellers'));
        return $resultPage;
    }
}
