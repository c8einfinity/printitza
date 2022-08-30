<?php

namespace Designnbuy\Reseller\Controller\Adminhtml\Resellers;


class ApplyTheme extends \Magento\Backend\App\Action
{
    protected $storeFactory;
    
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Store\Model\StoreFactory $storeFactory
    )
    {
        $this->storeFactory = $storeFactory;
        parent::__construct($context);
    }
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        try {
            $id = $this->getRequest()->getParam('id');

            $reseller = $this->_objectManager->create('Designnbuy\Reseller\Model\Resellers');
            if ($id) {
                $reseller->load($id);

                $store = $this->storeFactory->create();
                $store->load($reseller->getStoreId());
                
                $storeCode = $store->getCode();

                system('php bin/magento weltpixel:theme:configurator --store='.$storeCode.' --homePage=v1 --header=v1 --categoryPage=2columns --productPage=v1 --footer=v3');
                system('php bin/magento weltpixel:cleanup');
                system('php bin/magento weltpixel:less:generate');
                system('php bin/magento weltpixel:css:generate --store='.$storeCode.'');
                system('php bin/magento ok:urlrewrites:regenerate --storeId='.$reseller->getStoreId().'');
            }

            //system('php bin/magento indexer:reindex');

            $this->messageManager->addSuccess(
                __('Theme applied successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }
}
