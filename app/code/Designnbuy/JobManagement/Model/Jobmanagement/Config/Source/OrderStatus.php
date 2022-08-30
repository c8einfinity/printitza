<?php

namespace Designnbuy\JobManagement\Model\Jobmanagement\Config\Source;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    protected $options;

    /**
     * @param \Magento\Sales\Ui\Component\Listing\Column\Status\Options $orderOptions
     * @param array $data
     */

    public function __construct(
        \Magento\Sales\Ui\Component\Listing\Column\Status\Options $orderOptions
    ) {
        $this->_orderOptions = $orderOptions;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $orderStatus = $this->_orderOptions->toOptionArray();
        if ($this->options === null) {
            foreach ($orderStatus as $status) {
                $this->options[] = ['value' => $status['value'], 'label' => $status['label'],];
            }        
        }
        return $this->options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
