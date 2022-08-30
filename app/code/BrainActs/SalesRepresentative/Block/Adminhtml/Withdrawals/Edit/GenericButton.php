<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Block\Adminhtml\Withdrawals\Edit;

use Magento\Backend\Block\Widget\Context;
use BrainActs\SalesRepresentative\Api\WithdrawalsRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class GenericButton
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class GenericButton
{
    /**
     * @var Context
     */
    public $context;

    /**
     * @var WithdrawalsRepositoryInterface
     */
    public $withdrawalRepository;

    /**
     * @param Context $context
     * @param WithdrawalsRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context,
        WithdrawalsRepositoryInterface $blockRepository
    ) {
        $this->context = $context;
        $this->withdrawalRepository = $blockRepository;
    }

    /**
     * Return Member ID
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getWithdrawalId()
    {
        try {
            return $this->withdrawalRepository->getById(
                $this->context->getRequest()->getParam('withdrawal_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
            return null;
        }
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
