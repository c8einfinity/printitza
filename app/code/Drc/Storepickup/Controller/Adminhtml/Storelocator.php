<?php
/**
 * {{Drc}}_{{Storepickup}} extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  {{Drc}}
 *                     @package   {{Drc}}_{{Storepickup}}
 *                     @copyright Copyright (c) {{2016}}
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Drc\Storepickup\Controller\Adminhtml;

abstract class Storelocator extends \Magento\Backend\App\Action
{
    /**
     * Storelocator Factory
     *
     * @var \Drc\Storepickup\Model\StorelocatorFactory
     */
    protected $storelocatorFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var \Magento\Backend\Model\View\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * constructor
     *
     * @param \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Drc\Storepickup\Model\StorelocatorFactory $storelocatorFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->storelocatorFactory   = $storelocatorFactory;
        $this->coreRegistry          = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Init Storelocator
     *
     * @return \Drc\Storepickup\Model\Storelocator
     */
    protected function initStorelocator()
    {
        $storelocatorId  = (int) $this->getRequest()->getParam('storelocator_id');
        /** @var \Drc\Storepickup\Model\Storelocator $storelocator */
        $storelocator    = $this->storelocatorFactory->create();
        if ($storelocatorId) {
            $storelocator->load($storelocatorId);
        }
        $this->coreRegistry->register('drc_storepickup_storelocator', $storelocator);
        return $storelocator;
    }
}
