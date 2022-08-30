<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionTemplates\Model\Group;

use MageWorx\OptionTemplates\Model\Group;
use MageWorx\OptionTemplates\Model\GroupFactory;
use MageWorx\OptionTemplates\Model\ResourceModel\Group as ResourceModel;
use MageWorx\OptionTemplates\Model\ResourceModel\Group\Option as OptionResourceModel;

/**
 * Group copier. Creates group duplicate
 */
class Copier
{
    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var ResourceModel
     */
    protected $resourceModel;

    /**
     * @var OptionResourceModel
     */
    protected $optionResourceModel;

    /**
     * @param GroupFactory $groupFactory
     * @param ResourceModel $resourceModel
     * @param OptionResourceModel $optionResourceModel
     */
    public function __construct(
        GroupFactory $groupFactory,
        ResourceModel $resourceModel,
        OptionResourceModel $optionResourceModel
    ) {
        $this->groupFactory = $groupFactory;
        $this->resourceModel = $resourceModel;
        $this->optionResourceModel = $optionResourceModel;
    }

    /**
     * Create group duplicate
     *
     * @param Group $group
     * @return Group
     */
    public function copy(Group $group)
    {
        $duplicate = $this->groupFactory->create();
        $groupData = $group->getData();

        $duplicate->setData($groupData);
        $this->removeExcessData($duplicate);

        $isDuplicateSaved = false;
        do {
            $groupTitle = $duplicate->getTitle();
            $groupTitle = preg_match('/(.*)-(\d+)$/', $groupTitle, $matches)
                ? $matches[1] . '-' . ($matches[2] + 1)
                : $groupTitle . '-1';
            $duplicate->setTitle($groupTitle);
            try {
                if (!$this->resourceModel->isGroupTitleExist($duplicate)) {
                    $isDuplicateSaved = true;
                    $duplicate->save();
                }
            } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            }
        } while (!$isDuplicateSaved);

        $this->duplicateOptions($group->getGroupId(), $duplicate->getGroupId());

        return $duplicate;
    }

    /**
     * Remove excess data
     *
     * @param Group $duplicate
     * @return void
     */
    protected function removeExcessData(&$duplicate)
    {
        $duplicate->setUpdatedAt(null);
        $duplicate->setGroupId(null);
        $duplicate->setProducts([]);
        $duplicate->setProductsIds([]);
        $duplicate->setNewProductIds([]);
        $duplicate->setUpdProductIds([]);
        $duplicate->setDelProductIds([]);
        $duplicate->setAffectedProductIds([]);
        $duplicate->setOptions([]);
        $duplicate->setData('options', []);
        $duplicate->setData('product_options', []);
    }

    /**
     * Duplicate group custom options
     *
     * @param int $oldGroupId
     * @param int $newGroupId
     * @return void
     */
    public function duplicateOptions($oldGroupId, $newGroupId)
    {
        $this->optionResourceModel->duplicateOptions($oldGroupId, $newGroupId);
    }
}
