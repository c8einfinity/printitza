<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class FromTo
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class FromTo extends Column
{
    /**
     * Url path
     */
    const URL_PATH_EDIT = 'salesrep/member/edit';
    const URL_PATH_DELETE = 'salesrep/member/delete';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface
     */
    private $memberRepository;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \BrainActs\SalesRepresentative\Api\MemberRepositoryInterface $memberRepository,
        array $components = [],
        array $data = []
    ) {

        $this->urlBuilder = $urlBuilder;
        $this->memberRepository = $memberRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item[$this->getData('name')])) {
                    $data = $item[$this->getData('name')];
                    $members = unserialize($data);
                    $item[$this->getData('name')] = $this->getNamesByIds($members);
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $ids
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getNamesByIds($ids)
    {
        $names = [];
        foreach ($ids as $id) {
            $member = $this->memberRepository->getById($id);
            $names[] = implode(' ', [$member->getFirstname(), $member->getLastname()]);
        }

        $string = implode(', ', $names);

        if (empty($string)) {
            $string = __('N/A');
        }
        return $string;
    }
}
