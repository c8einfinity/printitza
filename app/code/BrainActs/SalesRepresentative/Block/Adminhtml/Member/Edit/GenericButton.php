<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace BrainActs\SalesRepresentative\Block\Adminhtml\Member\Edit;

use Magento\Backend\Block\Widget\Context;
use BrainActs\SalesRepresentative\Api\MemberRepositoryInterface;
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
     * @var MemberRepositoryInterface
     */
    public $memberRepository;

    /**
     * @param Context $context
     * @param MemberRepositoryInterface $blockRepository
     */
    public function __construct(
        Context $context,
        MemberRepositoryInterface $blockRepository
    ) {
        $this->context = $context;
        $this->memberRepository = $blockRepository;
    }

    /**
     * Return Member ID
     *
     * @return int|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockId()
    {
        try {
            return $this->memberRepository->getById(
                $this->context->getRequest()->getParam('member_id')
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
