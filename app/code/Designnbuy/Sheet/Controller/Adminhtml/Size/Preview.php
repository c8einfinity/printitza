<?php
/**
 * Copyright © Designnbuy (support@designnbuy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Designnbuy\Sheet\Controller\Adminhtml\Size;

/**
 * Sheet size preview controller
 */
class Preview extends \Designnbuy\Sheet\Controller\Adminhtml\Size
{
    public function execute()
    {
        try {
            $size = $this->_getModel();
            if (!$size->getId()) {
                throw new \Exception("Item is not longer exist.", 1);
            }

            $previewUrl = $this->_objectManager->get(\Designnbuy\Sheet\Model\PreviewUrl::class);
            $redirectUrl = $previewUrl->getUrl(
                $size,
                $previewUrl::CONTROLLER_SIZE
            );

            $this->getResponse()->setRedirect($redirectUrl);
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __('Something went wrong %1', $e->getMessage())
            );
            $this->_redirect('*/*/edit', [$this->_idKey => $size->getId()]);
        }
    }
}
