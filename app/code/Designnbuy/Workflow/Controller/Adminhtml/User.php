<?php


namespace Designnbuy\Workflow\Controller\Adminhtml;

abstract class User extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Designnbuy_Workflow::top_level';
    protected $_coreRegistry;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Designnbuy'), __('Designnbuy'))
            ->addBreadcrumb(__('User'), __('User'));
        return $resultPage;
    }
}
