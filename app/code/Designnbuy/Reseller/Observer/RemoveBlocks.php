<?php
namespace Designnbuy\Reseller\Observer;

use Magento\Framework\Event\ObserverInterface;

class RemoveBlocks implements ObserverInterface
{
    /**
     * @var \Designnbuy\Reseller\Model\Admin
     */
    protected $_reseller;

    public function __construct(
        \Designnbuy\Reseller\Model\Admin $reseller,
        \Designnbuy\Reseller\Helper\Data $resellerHelper,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->_reseller = $reseller;
        $this->_resellerHelper = $resellerHelper;
        $this->authSession = $authSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_reseller->isResellerAdmin()) {
            $layout = $observer->getLayout();
            $block = $layout->getBlock('column_output');
            if ($block) {
                $block->setTemplate(false);
            }            
        }

        $user = $this->authSession->getUser();
        if($user):
            $reseller = $this->_resellerHelper->isResellerUser($user->getId());
            if($reseller){
                $block = $layout->getBlock('designer.redemption.grid.massaction');
                if ($block) {
                    $layout->unsetElement('designer.redemption.grid.massaction');
                }
            }
        endif;        
        return;
    }
}