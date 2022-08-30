<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;

class MassStoreStatus extends \Magento\Backend\App\Action
{

    protected $storeFactory;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreFactory $storeFactory,
        \Designnbuy\Reseller\Model\Resellers $reseller
    )
    {
        $this->storeFactory = $storeFactory;
        $this->reseller = $reseller;
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
            $status = $this->getRequest()->getParam('storestatus');
            if (is_null($status)) {
                throw new \Exception(__('Parameter "Status" missing in request data.'));
            }
            $reseller = $this->reseller;
            foreach ($resellerIds as $resellerId) {
                $reseller = $reseller->load($resellerId);
                $store = $this->storeFactory->create();
                $store->load($reseller->getStoreId());
                if($store):
                    $store->setIsActive($status);
                    $store->save(); 
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
                __('Store Status have been changed.')
            );
        }
        $this->_redirect('*/*');
    }
}
