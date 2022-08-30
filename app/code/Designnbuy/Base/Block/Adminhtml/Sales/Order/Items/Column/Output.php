<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Base\Block\Adminhtml\Sales\Order\Items\Column;



/**
 * Sales Order items name column renderer
 */
class Output extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn
{
    /**
     * @var \Designnbuy\HotFolder\Helper\Data
     */

    private $hotFolderHelper;


    private $dnbBaseHelper;

    protected $authSession;
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Designnbuy\HotFolder\Helper\Data $hotFolderHelper,
        \Designnbuy\Base\Helper\Data $dnbBaseHelper,
        \Magento\Backend\Model\Auth\Session $authSession,
        array $data = []
    ) {
        $this->authSession = $authSession;
        $this->hotFolderHelper = $hotFolderHelper;
        $this->dnbBaseHelper = $dnbBaseHelper;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }


    public function getSubmitUrl()
    {
        $item = $this->getItem()->getId();
        return $this->getUrl('designnbuy_workflow/*/changeStatus', ['item_id' => $this->getItem()->getId()]);
    }

    public function getCurrentUser()
    {
        return $this->authSession->getUser();
    }

    public function hotFolderHelper()
    {
        return $this->hotFolderHelper;
    }

    public function dnbBaseHelper()
    {
        return $this->dnbBaseHelper;
    }
}
