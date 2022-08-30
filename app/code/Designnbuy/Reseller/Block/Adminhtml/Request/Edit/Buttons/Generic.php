<?php
/**
 * Designnbuy_Reseller extension
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 * 
 * @category  Designnbuy
 * @package   Designnbuy_Reseller
 * @copyright Copyright (c) 2018
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Designnbuy\Reseller\Block\Adminhtml\Request\Edit\Buttons;

class Generic
{
    /**
     * Widget Context
     * 
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * Request Repository
     * 
     * @var \Designnbuy\Reseller\Api\RequestRepositoryInterface
     */
    protected $requestRepository;

    /**
     * constructor
     * 
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Designnbuy\Reseller\Api\RequestRepositoryInterface $requestRepository
    ) {
        $this->context           = $context;
        $this->requestRepository = $requestRepository;
    }

    /**
     * Return Request ID
     *
     * @return int|null
     */
    public function getRequestId()
    {
        try {
            return $this->requestRepository->getById(
                $this->context->getRequest()->getParam('request_id')
            )->getId();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            return null;
        }
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
