<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Vendor\Block\Adminhtml\Transaction\Grid\Button;

use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Context;


/**
 * Class Generic
 */
class Generic
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Authorization\Model\Acl\AclRetriever $aclRetriever
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->authSession = $authSession;
        $this->aclRetriever = $aclRetriever;
    }
    /**
     * @param array $args
     * @return string
     */
    public function getSaveUrl(array $args = [])
    {
        $params = ['_current' => false, '_query' => false, 'store' => $this->getStoreId()];
        $params = array_merge($params, $args);
        return $this->getUrl('*/*/save', $params);
    }
    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    /**
     * Return ID
     *
     * @return int|null
     */
    public function getVendorId()
    {
        return $this->context->getRequest()->getParam('vendor_id');
    }


    /**
     * Return Store ID
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->context->getRequest()->getParam('store');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function isSuperAdmin()
    {
        $role = $this->getCurrentUser()->getRole();
        $resources = $this->aclRetriever->getAllowedResourcesByRole($role->getId());
        $resource = 'Designnbuy_Vendor::vendor';
        return in_array("Magento_Backend::all", $resources);

        //return $this->vendorHelper->isVendor();
    }
}
