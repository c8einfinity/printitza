<?php

namespace Designnbuy\Template\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class TemplateRepository implements \Designnbuy\Template\Api\TemplateRepositoryInterface
{
    /**
     * @var Template[]
     */
    protected $instances = [];

    /**
     * @var \Designnbuy\Template\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Designnbuy\Template\Model\ResourceModel\Template
     */
    protected $resourceModel;

    /**
     * @param \Designnbuy\Template\Model\TemplateFactory $templateFactory
     * @param \Designnbuy\Template\Model\ResourceModel\Template $resourceModel
     */
    public function __construct(
        \Designnbuy\Template\Model\TemplateFactory $templateFactory,
        \Designnbuy\Template\Model\ResourceModel\Template $resourceModel
    ) {
        $this->templateFactory = $templateFactory;
        $this->resourceModel = $resourceModel;
    }

    /**
     * @inheritdoc
     */
    public function save(\Designnbuy\Template\Api\Data\TemplateInterface $template)
    {
        try {
            $this->resourceModel->save($template);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __(
                    'Could not save template: %1',
                    $e->getMessage()
                ),
                $e
            );
        }
        unset($this->instances[$template->getId()]);
        return $template;
    }

    /**
     * @inheritdoc
     */
    public function get($templateId, $storeId = null)
    {
        $cacheKey = null !== $storeId ? $storeId : 'all';
        if (!isset($this->instances[$templateId][$cacheKey])) {
            /** @var Template $template */
            $template = $this->templateFactory->create();
            if (null !== $storeId) {
                $template->setStoreId($storeId);
            }
            $template->load($templateId);
            if (!$template->getId()) {
                throw NoSuchEntityException::singleField('id', $templateId);
            }
            $this->instances[$templateId][$cacheKey] = $template;
        }
        return $this->instances[$templateId][$cacheKey];
    }

    /**
     * @inheritdoc
     */
    public function delete(\Designnbuy\Template\Api\Data\TemplateInterface $template)
    {
        try {
            $templateId = $template->getId();
            $this->resourceModel->delete($template);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\StateException(
                __(
                    'Cannot delete template with id %1',
                    $template->getId()
                ),
                $e
            );
        }
        unset($this->instances[$templateId]);
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteById($id)
    {
        $template = $this->get($id);
        return $this->delete($template);
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