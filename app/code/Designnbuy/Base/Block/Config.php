<?php
/**
 * Copyright © 2016 MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace Designnbuy\Base\Block;

use \Magento\Catalog\Model\Product\Option\Repository as OptionRepository;
use \MageWorx\OptionDependency\Model\Config as ConfigModel;
use \Magento\Framework\Json\Helper\Data as JsonHelper;
use \Magento\Framework\Registry;
use \Magento\Framework\View\Element\Template\Context;
use \MageWorx\OptionBase\Helper\Data as OptionBaseHelper;

/**
 * Autocomplete class used to paste config data
 */
class Config extends \Magento\Framework\View\Element\Template
{
    /**
     * @var OptionBaseHelper
     */
    protected $helper;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var ConfigModel
     */
    protected $modelConfig;

    /**
     * @var Registry
     */
    protected $registry;
    protected $product_id;

    /**
     * @var OptionRepository
     */
    protected $productOptionsRepository;

    /**
     * Config constructor.
     * @param ConfigModel $modelConfig
     * @param JsonHelper $jsonHelper
     * @param Registry $registry
     * @param OptionRepository $repository
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        ConfigModel $modelConfig,
        JsonHelper $jsonHelper,
        Registry $registry,
        OptionRepository $repository,
        Context $context,
        OptionBaseHelper $helper,
        array $data = []
    ) {
        $this->modelConfig = $modelConfig;
        $this->jsonHelper = $jsonHelper;
        $this->registry = $registry;
        $this->productOptionsRepository = $repository;
        $this->helper = $helper;
        parent::__construct($context, $data);

    }

    /**
     * Get config json data
     * @return string JSON
     */
    public function getJsonData($id)
    {
        $this->product_id=$id;
        $data = [
            'optionParents' => $this->getOptionParents(),
            'valueParents' => $this->getValueParents(),
            'optionChildren' => $this->getValueParents(),
            'valueChildren' => $this->getValueChildren(),
            'optionTypes' => $this->getOptionTypes(),
            'optionRequiredConfig' => $this->getOptionsRequiredParam(),
        ];

        return $this->jsonHelper->jsonEncode($data);
    }

    /**
     * Get 'child_option_id' - 'parent_option_type_id' pairs in json
     * @return array
     */
    public function getOptionParents()
    {
        return $this->modelConfig->getOptionParents($this->product_id);
    }

    /**
     * Get 'child_option_type_id' - 'parent_option_type_id' pairs in json
     * @return array
     */
    public function getValueParents()
    {
        return $this->modelConfig->getValueParents($this->product_id);
    }

    /**
     * Get 'parent_option_type_id' - 'child_option_id' pairs in json
     * @return array
     */
    public function getOptionChildren()
    {
        return $this->modelConfig->getOptionChildren($this->product_id);
    }

    /**
     * Get 'parent_option_type_id' - 'child_option_type_id' pairs in json
     * @return array
     */
    public function getValueChildren()
    {
        return $this->modelConfig->getValueChildren($this->product_id);
    }

    /**
     * Get option types ('mageworx_option_id' => 'type') in json
     * @return array
     */
    public function getOptionTypes()
    {
        return $this->modelConfig->getOptionTypes($this->product_id);
    }

    /**
     * Returns array with key -> mageworx option ID , value -> is option required
     * Used in the admin area during order creation to add a valid css classes when toggle option based on dependencies
     *
     * @return array
     */
    public function getOptionsRequiredParam()
    {
        $config = [];
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->getProduct();
        /** @var \Magento\Catalog\Model\Product\Option[] $options */
        $options = $product->getOptions();
        foreach ($options as $option) {
            // Use raw option from the repository because product options miss original required parameter
            // Sometime it is set as false where in the original option it is true
            /** @var \Magento\Catalog\Api\Data\ProductCustomOptionInterface $rawOption */
            $rawOption = $this->productOptionsRepository->get($product->getSku(), $option->getId());
            $config[$option->getId()] = (bool)$rawOption->getIsRequire();
            $config[$option->getData('mageworx_option_id')] = (bool)$rawOption->getIsRequire();
        }

        return $config;
    }

    public function getProduct()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $product=$objectManager->get('\Magento\Catalog\Model\ProductFactory');
        return $product->create()->load($this->product_id);
    }

    /**
     * Get product id
     * @return string
     */
    public function setProductId($id)
    {
      $this->product_id = $id;
    }

    protected function getProductId()
    {
        return $this->product_id;
    }
}
