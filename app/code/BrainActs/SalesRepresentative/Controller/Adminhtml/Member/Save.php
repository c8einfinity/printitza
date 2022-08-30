<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Member;

use BrainActs\SalesRepresentative\Model\Member;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor
    ) {
    
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('member_id');

            if (isset($data['is_active']) && $data['is_active'] === 'true') {
                $data['is_active'] = Member::STATUS_ENABLED;
            }

            if (empty($data['member_id'])) {
                $data['member_id'] = null;
            }

            /** @var \BrainActs\SalesRepresentative\Model\Member $model */
            $model = $this->_objectManager->create('BrainActs\SalesRepresentative\Model\Member')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This member no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the member.'));
                $this->dataPersistor->clear('salesrep_member');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['member_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the member.'));
            }

            $this->dataPersistor->set('salesrep_member', $data);
            return $resultRedirect->setPath('*/*/edit', ['member_id' => $this->getRequest()->getParam('member_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
