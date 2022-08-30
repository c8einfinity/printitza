<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Background\Model\Background\Unit;

use Magento\Framework\Data\OptionSourceInterface;
use \Designnbuy\Base\Model\Product\Attribute\Source\BaseUnit;
/**
 * Class Unit
 */
class Source implements OptionSourceInterface
{

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param BuilderInterface $pageLayoutBuilder
     */
    public function __construct(BaseUnit $baseUnit)
    {
        $this->baseUnit = $baseUnit;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {

        return $this->baseUnit->getAllOptions();
        /* if ($this->options !== null) {
            return $this->options;
        }
        $options = [];
        
        $options = [
            ['value'=>'mm', 'label'=> __('Millimeters')],
            ['value'=>'cm', 'label'=> __('Centimeters')],
            ['value'=>'in', 'label'=> __('Inches')],
            ['value'=>'px', 'label'=> __('Pixels')],
        ];

        $this->options = $options;

        return $this->options; */
    }
}
