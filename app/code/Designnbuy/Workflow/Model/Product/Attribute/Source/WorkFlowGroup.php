<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Workflow\Model\Product\Attribute\Source;

class WorkFlowGroup extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_options;
    /**
     * @var \Designnbuy\Workflow\Model\ResourceModel\Group\CollectionFactory
     */
    protected $_groupFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Designnbuy\Workflow\Model\ResourceModel\Group\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Designnbuy\Workflow\Model\ResourceModel\Group\CollectionFactory $groupFactory
    ) {
        $this->_groupFactory = $groupFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = $this->_groupFactory->create()->addFieldToFilter('status',1)->toOptionArray();
            array_unshift($this->_options, ['value' => '', 'label' => __('Please Select Group')]);
        }
        return $this->_options;
    }

}
