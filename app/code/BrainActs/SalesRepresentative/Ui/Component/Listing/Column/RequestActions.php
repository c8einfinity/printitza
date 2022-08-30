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
 * Class MemberActions
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class RequestActions extends Column
{
    /**
     * Url path
     */
    const URL_PATH_APPROVE = 'salesrep/customer/approve';
    const URL_PATH_REJECT = 'salesrep/customer/reject';

    /**
     * @var UrlInterface
     */
    public $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {

        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['confirm_id'])) {
                    $item[$this->getData('name')] = [
                        'approve' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_APPROVE,
                                [
                                    'id' => $item['confirm_id']
                                ]
                            ),
                            'label' => __('Approve'),
                            'confirm' => [
                                'title' => __('Approve request for "${ $.$data.customer_name }"'),
                                'message' => __('Are you sure you wan\'t to approve this request?')
                            ]
                        ],
                        'reject' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_REJECT,
                                [
                                    'id' => $item['confirm_id']
                                ]
                            ),
                            'label' => __('Reject'),
                            'confirm' => [
                                'title' => __('Reject request for "${ $.$data.customer_name }"'),
                                'message' => __('Are you sure you wan\'t to reject this request?')
                            ]
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}
