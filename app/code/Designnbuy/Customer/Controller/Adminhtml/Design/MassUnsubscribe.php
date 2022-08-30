<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Design;

class MassUnsubscribe extends \Designnbuy\Customer\Controller\Adminhtml\Design
{
    /**
     * Unsubscribe one or more designs action
     *
     * @return void
     */
    public function execute()
    {
        $designsIds = $this->getRequest()->getParam('design');
        if (!is_array($designsIds)) {
            $this->messageManager->addError(__('Please select one or more designs.'));
        } else {
            try {
                foreach ($designsIds as $designId) {
                    $design = $this->_objectManager->create(
                        'Designnbuy\Customer\Model\Design'
                    )->load(
                        $designId
                    );
                    $design->unsubscribe();
                }
                $this->messageManager->addSuccess(__('A total of %1 record(s) were updated.', count($designsIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
