<?php

namespace Eyemagine\HubSpot\Model\Adminhtml\System\Config\Source\Customer;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory;

/**
 * Class Group
 * @package Eyemagine\HubSpot\Model\Adminhtml\System\Config\Source\Customer
 */
class Group implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var CollectionFactory
     */
    protected $groupCollectionFactory;

    /**
     * @var
     */
    protected $options;

    /**
     * Group constructor.
     * @param CollectionFactory $groupCollectionFactory
     */
    public function __construct(
        CollectionFactory $groupCollectionFactory
    )
    {
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->options) {
            $this->options = $this->groupCollectionFactory->create()->loadData()->toOptionArray();
        }
        return $this->options;
    }
}
