<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\User\Model\User as AdminUser;

/**
 * Class User
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class User implements OptionSourceInterface
{

    /**
     * @var \Magento\User\Model\User
     */
    private $userModel;
    /**
     * @var array
     */
    private $options;

    /**
     * User constructor.
     * @param \Magento\User\Model\User $userModel
     */
    public function __construct(AdminUser $userModel)
    {
        $this->userModel = $userModel;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $collection = $this->userModel->getCollection();

        $options = [];
        foreach ($collection as $user) {
            $options[] = [
                'label' => implode(',', [$user->getFirstname(), $user->getLastName()]),
                'value' => $user->getId(),
            ];
        }

        $this->options = $options;

        return $this->options;
    }
}
