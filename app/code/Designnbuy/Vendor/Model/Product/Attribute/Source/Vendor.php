<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 *
 * Catalog product base_unit attribute source
 */
namespace Designnbuy\Vendor\Model\Product\Attribute\Source;

class Vendor extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var array
     */
    protected $_options = [];
    /**
     * @var \Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory
     */
    protected $_vendorCollectionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Factory for user role model
     *
     * @var \Magento\Authorization\Model\RoleFactory
     */
    protected $_roleFactory;


    /**
     * Factory for user model
     *
     * @var  \Magento\User\Model\ResourceModel\User\CollectionFactory
     */
    protected $userCollectionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory $userRolesFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        \Designnbuy\Vendor\Model\ResourceModel\User\CollectionFactory $vendorCollectionFactory,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\User\Model\ResourceModel\User\CollectionFactory $userCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_vendorCollectionFactory = $vendorCollectionFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->_roleFactory = $roleFactory;
        $this->userCollectionFactory = $userCollectionFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $defaultRoleId = $this->_scopeConfig->getValue(
            'dnbvendor/settings/default_role',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        
        $users = $this->_roleFactory->create()->setId($defaultRoleId)->getRoleUsers();
        if (!$this->_options) {
            $userCollection = $this->userCollectionFactory
                ->create()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('main_table.user_id', array('in' => $users));
            foreach ($userCollection as $user) {
                $this->_options[] = [
                    'value' => $user->getUserId(),
                    'label' => $user->getFirstname().' '.$user->getLastname()
                ];
            }
            array_unshift($this->_options, ['value' => '', 'label' => __('Please Select Vendor')]);
        }

        return $this->_options;
    }

}
