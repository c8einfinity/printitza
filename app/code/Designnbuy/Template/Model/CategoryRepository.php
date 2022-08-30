<?php

namespace Designnbuy\Template\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class CategoryRepository implements \Designnbuy\Template\Api\Category\CategoryRepositoryInterface
{
    /**
     * @var Template[]
     */
    protected $instances = [];

    /**
     * @var \Designnbuy\Template\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template
     */
    protected $resourceModel;

    /**
     * @param \Designnbuy\Template\Model\CategoryFactory $categoryFactory
     * @param \Designnbuy\Template\Model\ResourceModel\Template $resourceModel
     */
    public function __construct(
        \Designnbuy\Template\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Template\Model\ResourceModel\Category $resourceModel
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function save(\Designnbuy\Template\Api\Data\Category\CategoryInterface $category)
    {
        try {
            $this->resourceModel->save($category);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save template: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$category->getId()]);
        return $category;
    }

    /**
     * @inheritdoc
     */
    public function get($categoryId, $storeId = null)
    {
        $cacheKey = null !== $storeId ? $storeId : 'all';
        if (!isset($this->instances[$categoryId][$cacheKey])) {
            /** @var Template $category */
            $category = $this->categoryFactory->create();
            if (null !== $storeId) {
                $category->setStoreId($storeId);
            }
            $category->load($categoryId);
            if (!$category->getId()) {
                throw NoSuchEntityException::singleField('id', $categoryId);
            }
            $this->instances[$categoryId][$cacheKey] = $category;
        }
        return $this->instances[$categoryId][$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public function delete(\Designnbuy\Template\Api\Data\Category\CategoryInterface $category)
    {
        try {
            $categoryId = $category->getId();
            $this->resourceModel->delete($category);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __(
                    'Cannot delete category with id %1',
                    $category->getId()
                ),
                $e
            );
        }
        unset($this->instances[$categoryId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $category = $this->get($id);
        return $this->delete($category);
    }

    /**
     * @inheritdoc
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        // TODO: Implement getList method
        throw new \BadMethodCallException(__CLASS__.'::'.__METHOD__.' has not been implemented yet');
    }
}