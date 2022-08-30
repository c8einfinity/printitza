<?php

namespace Unific\Connector\Helper\Data;

use Magento\Catalog\Api\Data\CategoryInterface;

class Category
{
    /**
     * @var Converter
     */
    protected $outputProcessor;

    // Holds a Product API DATA object
    protected $category;

    protected $returnData = [];

    /**
     * @param Converter $outputProcessor
     */
    public function __construct(
        Converter $outputProcessor
    ) {
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * @param CategoryInterface $category
     */
    public function setCategory(CategoryInterface $category)
    {
        $this->category = $category;
        $this->setCategoryInfo();
    }

    /**
     * @return CategoryInterface
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return void
     */
    protected function setCategoryInfo()
    {
        $this->returnData = $this->outputProcessor->convertValue(
            $this->category,
            CategoryInterface::class
        );
    }

    /**
     * @return array
     */
    public function getCategoryInfo()
    {
        return $this->returnData;
    }
}
