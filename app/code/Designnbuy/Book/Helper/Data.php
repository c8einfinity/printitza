<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Book\Helper;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
/**
 * Designnbuy Book Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**#@+
     * Product Base Unit values
     */


    /**
     * Media Directory object (writable).
     *
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_mediaDirectory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $eavSetupFactory;
    /**
     * EAV setup factory
     *
     * @var EavSetupFactory
     */
    private $setup;

    /**
     * Init
     * @param MathDivision $mathDivision
     * @param FormatInterface $localeFormat
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param EavSetupFactory $eavSetupFactory
     * @param EncoderInterface $jsonEncoder
     * @param DecoderInterface $jsonDecoder
     * @param ProductRepositoryInterface $productRepository
     * @param StockStateProviderInterface $stockStateProvider
     * @param StockRegistryProviderInterface $stockRegistryProvider
     * @param StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param Quote\Item\Processor $itemProcessor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        EavSetupFactory $eavSetupFactory,
        ModuleDataSetupInterface $setup
    ){
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->setup = $setup;

    }

    public function getCustomBookAttributeSetId()
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->setup]);
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $customBookAttributeSetId = $eavSetup->getAttributeSet($entityTypeId, 'CustomBook', 'attribute_set_id');
        $attributeSetId = $customBookAttributeSetId;//$eavSetup->getAttributeSetId($entityTypeId, 'CustomBook');
        return $attributeSetId;
    }

    public function isBookProduct($product)
    {
        if($product->getAttributeSetId() == $this->getCustomBookAttributeSetId()){
            return true;
        }
        return false;
    }

}

