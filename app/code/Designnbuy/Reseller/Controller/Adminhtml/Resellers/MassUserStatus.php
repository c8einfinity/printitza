<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

class MassUserStatus extends \Magento\Backend\App\Action
{
    protected $storeFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Designnbuy\Reseller\Model\Resellers $reseller,
        \Magento\User\Model\User $user
    )
    {
        $this->reseller = $reseller;
        $this->user = $user;
        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resellerIds = $this->getRequest()->getParam('reseller');
        $error = false;
        try {
            $status = $this->getRequest()->getParam('userstatus');
            if (is_null($status)) {
                throw new \Exception(__('Parameter "Status" missing in request data.'));
            }
            $reseller = $this->reseller;
            foreach ($resellerIds as $resellerId) {
                $reseller = $reseller->load($resellerId);
                
                $adminUser = $this->user;
                $adminUser->load($reseller->getUserId());
                if($adminUser):
                    $adminUser->setIsActive($status);
                    $adminUser->save();
                endif;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $error = true;
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $error = true;
            $this->messageManager->addException(
                $e,
                __(
                    "We can't change status of right now.",
                    $e->getMessage()
                )
            );
        }

        if (!$error) {
            $this->messageManager->addSuccess(
                __('User Status have been changed.')
            );
        }
        $this->_redirect('*/*');
    }
}
