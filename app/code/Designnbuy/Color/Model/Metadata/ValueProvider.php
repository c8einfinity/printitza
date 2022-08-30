<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Color\Model\Metadata;

use Designnbuy\Color\Model\ResourceModel\Category\Collection;
use Designnbuy\Color\Model\Category;
use Magento\Store\Model\System\Store;
use Magento\Framework\Convert\DataObject;

/**
 * Metadata provider for sales rule edit form.
 */
class ValueProvider
{
    /**
     * @var Store
     */
    protected $store;


    /**
     * @var DataObject
     */
    protected $objectConverter;


    /**
     * Initialize dependencies.
     *
     * @param Store $store
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     */
    public function __construct(
        Store $store,
        DataObject $objectConverter
    ) {
        $this->store = $store;
        $this->objectConverter = $objectConverter;
    }

    /**
     * Get metadata for sales rule form. It will be merged with form UI component declaration.
     *
     * @param Rule $rule
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getMetadataValues($currentModel)
    {
        $labels = array();
        if($currentModel){
            $labels = $currentModel->getStoreLabels();
        }

        return [
            'labels' => [
                'children' => [
                    'store_labels[0]' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'value' => isset($labels[0]) ? $labels[0] : '',
                                ],
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }
}
