<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Config;

/**
 * Class Members
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Members implements \Magento\Framework\Data\OptionSourceInterface
{

    /**
     * @var \BrainActs\SalesRepresentative\Model\MemberFactory
     */
    private $memberFactory;

    public function __construct(
        \BrainActs\SalesRepresentative\Model\MemberFactory $memberFactory
    ) {
    
        $this->memberFactory = $memberFactory;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $options = [];

        $collection = $this->memberFactory->create()->getCollection();

        foreach ($collection as $member) {
            $options[] = [
                'label' => implode(' ', [$member->getFirstname(), $member->getLastname()]),
                'value' => $member->getId()
            ];
        }
        return $options;
    }
}
