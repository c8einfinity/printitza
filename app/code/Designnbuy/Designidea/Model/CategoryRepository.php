<?php

namespace Designnbuy\Designidea\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class CategoryRepository implements \Designnbuy\Designidea\Api\Category\CategoryRepositoryInterface
{
    /**
     * @var Designidea[]
     */
    protected $instances = [];

    /**
     * @var \Designnbuy\Designidea\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea
     */
    protected $resourceModel;

    /**
     * @param \Designnbuy\Designidea\Model\CategoryFactory $categoryFactory
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea $resourceModel
     */
    public function __construct(
        \Designnbuy\Designidea\Model\CategoryFactory $categoryFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Category $resourceModel
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function save(\Designnbuy\Designidea\Api\Data\Category\CategoryInterface $category)
    {
        try {
            $this->resourceModel->save($category);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save designidea: %1',
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
            /** @var Designidea $category */
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
    public function delete(\Designnbuy\Designidea\Api\Data\Category\CategoryInterface $category)
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