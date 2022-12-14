<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Controller\Adminhtml\Product;

use Designnbuy\DesignReview\Controller\Adminhtml\Product as ProductController;
use Magento\Framework\Controller\ResultFactory;

class Delete extends ProductController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $reviewId = $this->getRequest()->getParam('id', false);
        try {
            $this->reviewFactory->create()->setId($reviewId)->aggregate()->delete();

            $this->messageManager->addSuccess(__('The review has been deleted.'));
            if ($this->getRequest()->getParam('ret') == 'pending') {
                $resultRedirect->setPath('designreview/*/pending');
            } else {
                $resultRedirect->setPath('designreview/*/');
            }
            return $resultRedirect;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong  deleting this review.'));
        }

        return $resultRedirect->setPath('designreview/*/edit/', ['id' => $reviewId]);
    }
}
