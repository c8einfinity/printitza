<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Withdrawals;

use Magento\Backend\App\Action\Context;
use BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface as WithdrawalsRepository;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Cms\Api\Data\PageInterface;

/**
 * Class InlineEdit
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_withdrawals_save';

    /**
     * @var \BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface
     */
    public $withdrawalRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    public $jsonFactory;

    /**
     * InlineEdit constructor.
     * @param Context $context
     * @param WithdrawalsRepository $withdrawalRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        WithdrawalsRepository $withdrawalRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);

        $this->withdrawalRepository = $withdrawalRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $withdrawalId) {
            /** @var \BrainActs\SalesRepresentative\Model\Withdrawals $withdrawal */
            $withdrawal = $this->withdrawalRepository->getById($withdrawalId);
            try {
                $withdrawalData = $postItems[$withdrawalId];
                $extendedPageData = $withdrawal->getData();
                $this->setWithdrawalData($withdrawal, $extendedPageData, $withdrawalData);
                $this->withdrawalRepository->save($withdrawal);//@codingStandardsIgnoreLine
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $messages[] = $this->getErrorWithPageId(
                    $withdrawal,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithPageId(
                    $withdrawal,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithPageId(
                    $withdrawal,
                    __('Something went wrong while saving the page.')
                );
                $error = true;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }

    /**
     * @param \BrainActs\SalesRepresentative\Model\Withdrawals $withdrawal
     * @param array $extendedWithdrawalData
     * @param array $withdrawalData
     * @return $this
     */
    private function setWithdrawalData(
        \BrainActs\SalesRepresentative\Model\Withdrawals $withdrawal,
        array $extendedWithdrawalData,
        array $withdrawalData
    ) {
        $withdrawal->setData(array_merge($withdrawal->getData(), $extendedWithdrawalData, $withdrawalData));
        return $this;
    }
}
