<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Design;

class Delete extends \Designnbuy\Customer\Controller\Manage
{

    /**
     * Managing customer subscription page
     *
     * @return void
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('design_id');
        if ($id) {
            try {
                $this->_objectManager->create('Designnbuy\Customer\Model\Design')->setId($id)->delete();
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t remove the design.'));
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
            }
        }
        $defaultUrl = $this->_objectManager->create(\Magento\Framework\UrlInterface::class)->getUrl('*/*');
        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->getRedirectUrl($defaultUrl));
    }
}
