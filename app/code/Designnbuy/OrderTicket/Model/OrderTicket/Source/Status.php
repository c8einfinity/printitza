<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\OrderTicket\Source;

/**
 * ORDERTICKET Item status attribute model
 */
class Status extends \Designnbuy\OrderTicket\Model\OrderTicket\Source\AbstractSource
{
    /**
     * Status constants
     */
    const STATE_PENDING = 'pending';

    const STATE_OPEN = 'open';

    const STATE_CLOSED = 'closed';

    /**
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param \Designnbuy\OrderTicket\Model\Item\Attribute\Source\StatusFactory $statusFactory
     */
    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
    ) {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
    }

    /**
     * Get state label based on the code
     *
     * @param string $state
     * @return \Magento\Framework\Phrase|string
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getItemLabel($state)
    {
        switch ($state) {
            case self::STATE_PENDING:
                return __('Pending');
            case self::STATE_OPEN:
                return __('Open');
            case self::STATE_CLOSED:
                return __('Closed');
            default:
                return $state;
        }
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return [
            self::STATE_PENDING => __('Pending'),
            self::STATE_OPEN => __('Open'),
            self::STATE_CLOSED => __('Closed')
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }

    /**
     * Get available states keys for entities
     *
     * @return string[]
     */
    protected function _getAvailableValues()
    {
        return [
            self::STATE_PENDING,
            self::STATE_OPEN,
            self::STATE_CLOSED
        ];
    }

    /**
     * Get button disabled status
     *
     * @param string $status
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getButtonDisabledStatus($status)
    {
        if (in_array(
            $status,
            [
                self::STATE_OPEN,
                self::STATE_CLOSED
            ]
        )
        ) {
            return true;
        }
        return false;
    }
}
