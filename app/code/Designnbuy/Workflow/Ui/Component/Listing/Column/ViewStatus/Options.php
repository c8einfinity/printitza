<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Workflow\Ui\Component\Listing\Column\ViewStatus;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Store Options for Cms Pages and Blocks
 */
class Options implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;


    /**
     * @var \Designnbuy\Workflow\Model\StatusFactory
     */
    protected $statusFactory;

    /**
     * Initialize dependencies.
     *
     * @param \Designnbuy\Workflow\Model\StatusFactory $statusFactory
     * @param void
     */
    public function __construct(
        \Designnbuy\Workflow\Model\StatusFactory $statusFactory
    ) {
        $this->statusFactory = $statusFactory;
    }

    /**
     * Get options
     *
     * @return array
     */

    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = $this->_getOptions();
        }
        return $this->options;
    }

    protected function _getOptions()
    {
        $status = $this->statusFactory->create()->getCollection()->toOptionArray();
        return $status;
    }
}
