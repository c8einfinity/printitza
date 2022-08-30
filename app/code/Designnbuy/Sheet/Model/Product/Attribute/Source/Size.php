<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Sheet\Model\Product\Attribute\Source;

class Size extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * @var \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory
     */
    protected $_sizeCollectionFactory;

    /**
     * @var \Designnbuy\Sheet\Model\SizeFactory
     */
    protected $_sizeFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory $sizeCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Designnbuy\Sheet\Model\SizeFactory $sizeFactory,
        \Designnbuy\Sheet\Model\ResourceModel\Size\CollectionFactory $sizeCollectionFactory
    ) {
        $this->_sizeFactory = $sizeFactory;
        $this->_sizeCollectionFactory = $sizeCollectionFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_sizeCollectionFactory->create()->addFieldToFilter('is_active',1)->toOptionArray('size_id', 'title');
            array_unshift($this->_options, ['value' => '', 'label' => __('Please Select Size')]);
        }
        return $this->_options;
    }

    public function getOptionText($value)
    {
        $size = $this->_sizeFactory->create()->load($value);
        if($size && $size->getSizeId()){
            return $size->getWidth() .'x'. $size->getHeight();
        }
        return false;
    }

}
