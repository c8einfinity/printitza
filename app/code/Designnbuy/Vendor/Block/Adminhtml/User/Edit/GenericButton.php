<?php

namespace Designnbuy\Vendor\Block\Adminhtml\User\Edit;

use Magento\Backend\Block\Widget\Context;

abstract class GenericButton
{

    protected $context;

    protected $coreRegistry;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     */
    public function __construct(Context $context, \Magento\Framework\Registry $coreRegistry, \Designnbuy\Vendor\Helper\Data $vendorData)
    {
        $this->context = $context;
        $this->coreRegistry = $coreRegistry;
        $this->vendorData = $vendorData;
    }

    /**
     * Return model ID
     *
     * @return int|null
     */
    public function getModelId()
    {
        return $this->context->getRequest()->getParam('id');
    }

    public function getVendor(){
        return $this->coreRegistry->registry('designnbuy_vendor_user');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
