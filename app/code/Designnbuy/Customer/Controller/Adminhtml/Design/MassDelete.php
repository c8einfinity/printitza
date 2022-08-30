<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Design;

class MassDelete extends \Designnbuy\Customer\Controller\Adminhtml\Design
{
    /**
     * Delete one or more designs action
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
                    $design->delete();
                }
                $this->messageManager->addSuccess(__('Total of %1 record(s) were deleted.', count($designsIds)));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        $this->_redirect('*/*/index');
    }
}
