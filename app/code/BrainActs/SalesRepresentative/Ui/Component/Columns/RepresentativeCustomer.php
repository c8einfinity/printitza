<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Ui\Component\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Representative
 * @author BrainActs Core Team <support@brainacts.com>
 */
class RepresentativeCustomer extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member
     */
    private $memberResourceFactory;

    /**
     * RepresentativeCustomer constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory $memberResourceFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->memberResourceFactory = $memberResourceFactory;
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item[$this->getData('name')])) {
                    $value = $item[$this->getData('name')];

                    if (empty($value)) {
                        continue;
                    }

                    $list = explode(',', $value);

                    $size = count($list);

                    if ($size > 1) {
                        $item[$this->getData('name')] = 'Multi Links';
                    } elseif ($size == 1) {
                        $item[$this->getData('name')] = $item['member_name'];
                    }
                } else {
                    $item['representative'] = $this->getMembersByCustomer($item['entity_id']);
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $customerId
     * @return string
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getMembersByCustomer($customerId)
    {
        $resource = $this->memberResourceFactory->create();
        $members = $resource->getMembersByCustomerId($customerId);

        $names = [];
        foreach ($members as $member) {
            $names[] = implode(' ', $member);
        }
        return implode(', ', $names);
    }
}
