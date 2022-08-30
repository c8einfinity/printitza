<?php

namespace Designnbuy\Designidea\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class DesignideaRepository implements \Designnbuy\Designidea\Api\DesignideaRepositoryInterface
{
    /**
     * @var Designidea[]
     */
    protected $instances = [];

    /**
     * @var \Designnbuy\Designidea\Model\DesignideaFactory
     */
    protected $designIdeaFactory;

    /**
     * @var \Designnbuy\Designidea\Model\ResourceModel\Designidea
     */
    protected $resourceModel;

    /**
     * @param \Designnbuy\Designidea\Model\DesignideaFactory $designIdeaFactory
     * @param \Designnbuy\Designidea\Model\ResourceModel\Designidea $resourceModel
     */
    public function __construct(
        \Designnbuy\Designidea\Model\DesignideaFactory $designIdeaFactory,
        \Designnbuy\Designidea\Model\ResourceModel\Designidea $resourceModel
    ) {
        $this->designIdeaFactory = $designIdeaFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function save(\Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea)
    {
        try {
            $this->resourceModel->save($designidea);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save designidea: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$designidea->getId()]);
        return $designidea;
    }

    /**
     * @inheritdoc
     */
    public function get($designideaId, $storeId = null)
    {
        $cacheKey = null !== $storeId ? $storeId : 'all';
        if (!isset($this->instances[$designideaId][$cacheKey])) {
            /** @var Designidea $designidea */
            $designidea = $this->designIdeaFactory->create();
            if (null !== $storeId) {
                $designidea->setStoreId($storeId);
            }
            $designidea->load($designideaId);
            if (!$designidea->getId()) {
                throw NoSuchEntityException::singleField('id', $designideaId);
            }
            $this->instances[$designideaId][$cacheKey] = $designidea;
        }
        return $this->instances[$designideaId][$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public function delete(\Designnbuy\Designidea\Api\Data\DesignideaInterface $designidea)
    {
        try {
            $designideaId = $designidea->getId();
            $this->resourceModel->delete($designidea);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __(
                    'Cannot delete editable artwork with id %1',
                    $designidea->getId()
                ),
                $e
            );
        }
        unset($this->instances[$designideaId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $designidea = $this->get($id);
        return $this->delete($designidea);
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