<?php
namespace Designnbuy\Productattach\Controller\Adminhtml;

abstract class Fileicon extends \Magento\Backend\App\Action
{

    protected $_coreRegistry;
    const ADMIN_RESOURCE = 'Designnbuy_Productattach::manage';

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
        $resultPage->setActiveMenu('Designnbuy_Productattach::designnbuy_productattach_fileicon')
            ->addBreadcrumb(__('Designnbuy'), __('Designnbuy'))
            ->addBreadcrumb(__('Fileicon'), __('Fileicon'));
        return $resultPage;
    }
}
