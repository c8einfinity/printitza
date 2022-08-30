<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
namespace Designnbuy\Workflow\Model\System\Config\Source;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
class Role implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;
    /**
     * @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory
     */
    protected $_userRolesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $userRolesFactory
    ) {
        $this->_userRolesFactory = $userRolesFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            /** @var $stores \Magento\Authorization\Model\ResourceModel\Role\Collection */
            $roles = $this->_userRolesFactory->create();
            $roles->addFieldToFilter('role_type', RoleGroup::ROLE_TYPE);
            $this->_options = $roles->load()->toOptionArray();
        }
        return $this->_options;
    }

}
