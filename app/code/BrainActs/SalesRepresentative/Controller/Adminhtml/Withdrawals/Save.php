<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Withdrawals;

use Magento\Backend\App\Action;
use BrainActs\SalesRepresentative\Model\Withdrawals;
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
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_withdrawals_save';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \BrainActs\SalesRepresentative\Model\WithdrawalsFactory|null
     */
    private $withdrawalsFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface|null
     */
    private $withdrawalsRepository;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory
     */
    private $profitFactory;

    /**
     * Save constructor.
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param \BrainActs\SalesRepresentative\Model\WithdrawalsFactory|null $withdrawalsFactory
     * @param \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface|null $withdrawalsRepository
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor,
        \BrainActs\SalesRepresentative\Model\WithdrawalsFactory $withdrawalsFactory,
        \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface $withdrawalsRepository,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Report\ProfitFactory $profitFactory
    )
    {

        $this->dataPersistor = $dataPersistor;
        $this->withdrawalsFactory = $withdrawalsFactory;
        $this->withdrawalsRepository = $withdrawalsRepository;
        $this->profitFactory = $profitFactory;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            if (empty($data['withdrawal_id'])) {
                $data['withdrawal_id'] = null;
            }

            /** @var \BrainActs\SalesRepresentative\Model\Withdrawals $model */
            $model = $this->withdrawalsFactory->create();

            $id = $this->getRequest()->getParam('withdrawal_id');
            if ($id) {
                try {
                    $model = $this->withdrawalsRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This page no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $withdrawal = $this->withdrawalsRepository->save($model);

                //
                if (!$id) {
                    $this->setupProfitWithdrawal($withdrawal);
                } else {
                    $this->updateProfitWithdrawal($withdrawal);
                }

                $this->messageManager->addSuccessMessage(__('You saved the withdrawal.'));
                $this->dataPersistor->clear('salesrep_withdrawals');
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', [
                        'withdrawal_id' => $model->getId(),
                        '_current' => true
                    ]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the withdrawal.'));
            }

            $this->dataPersistor->set('salesrep_withdrawals', $data);
            return $resultRedirect->setPath('*/*/edit', [
                'withdrawal_id' => $this->getRequest()->getParam('withdrawal_id')
            ]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param Withdrawals $withdrawal
     * @throws \Exception
     */
    private function setupProfitWithdrawal(\BrainActs\SalesRepresentative\Model\Withdrawals $withdrawal)
    {
        $profitReport = $this->profitFactory->create();
        if ($withdrawal->getStatus() == \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_COMPLETED) {
            //create in profit table
            $profitReport->insertWithdrawal($withdrawal->getId(), $withdrawal->getAmount(), $withdrawal->getMemberId());
        }
    }

    /**
     * @param Withdrawals $withdrawal
     * @throws \Exception
     */
    private function updateProfitWithdrawal(\BrainActs\SalesRepresentative\Model\Withdrawals $withdrawal)
    {
        $profitReport = $this->profitFactory->create();
        $profitReport->removeWithdrawal($withdrawal->getId());

        if ($withdrawal->getStatus() == \BrainActs\SalesRepresentative\Model\Withdrawals::STATUS_COMPLETED) {
            //create in profit table
            $profitReport->insertWithdrawal($withdrawal->getId(), $withdrawal->getAmount(), $withdrawal->getMemberId());
        }
    }
}
