<?php
/**
 * Copyright © MageWorx. All rights reserved.
 * See LICENSE.txt for license details.
 */
namespace MageWorx\OptionTemplates\Controller\Adminhtml\Group;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Helper\Js as JsHelper;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use MageWorx\OptionBase\Model\Product\Attributes as ProductAttributes;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group as GroupController;
use MageWorx\OptionTemplates\Model\Group\Source\AssignType;
use MageWorx\OptionTemplates\Model\OptionSaver;
use MageWorx\OptionTemplates\Model\GroupFactory;
use MageWorx\OptionTemplates\Model\Group\Option as GroupOptionModel;
use MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder as GroupBuilder;
use MageWorx\OptionTemplates\Model\Group\Copier;

class Save extends GroupController
{
    /**
     * @var OptionSaver
     */
    protected $optionSaver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var JsHelper
     */
    protected $jsHelper;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var GroupFactory
     */
    protected $groupFactory;

    /**
     * @var GroupOptionModel
     */
    protected $groupOptionModel;

    /**
     * @var ProductAttributes
     */
    protected $productAttributes;

    /**
     * @var Copier
     */
    protected $groupCopier;

    /**
     * @var array
     */
    protected $formData = [];

    /**
     * @param ProductCollectionFactory $productCollectionFactory
     * @param OptionSaver $optionSaver
     * @param JsHelper $jsHelper
     * @param GroupBuilder $groupBuilder
     * @param GroupFactory $groupFactory
     * @param GroupOptionModel $groupOptionModel
     * @param ProductAttributes $productAttributes
     * @param Context $context
     * @param Copier $groupCopier
     * @param Registry $registry
     */
    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        OptionSaver $optionSaver,
        JsHelper $jsHelper,
        \MageWorx\OptionTemplates\Controller\Adminhtml\Group\Builder $groupBuilder,
        GroupFactory $groupFactory,
        GroupOptionModel $groupOptionModel,
        ProductAttributes $productAttributes,
        Context $context,
        Copier $groupCopier,
        Registry $registry
    ) {
        $this->optionSaver = $optionSaver;
        $this->jsHelper = $jsHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->registry = $registry;
        $this->groupFactory = $groupFactory;
        $this->groupOptionModel = $groupOptionModel;
        $this->productAttributes = $productAttributes;
        $this->groupCopier = $groupCopier;
        parent::__construct($groupBuilder, $context);
    }

    /**
     * Run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {

        $resultRedirect = $this->resultRedirectFactory->create();
        $this->registry->unregister('mageworx_optiontemplates_group_save');
        $this->registry->register('mageworx_optiontemplates_group_save', true);

        $redirectBack = $this->getRequest()->getParam('back', false);
        $data = $this->getRequest()->getParam('mageworx_optiontemplates_group');
        if (!$data) {
            $this->registry->unregister('mageworx_optiontemplates_group_save');
            $resultRedirect->setPath('mageworx_optiontemplates/*/');
            return $resultRedirect;
        }

        $this->formData = $data;
        if (empty($this->formData['options'])) {
            $this->formData['options'] = [];
        }

        $isTemplateChanged = true;
        if ($this->isExistingTemplate()) {
            /** @var \MageWorx\OptionTemplates\Model\Group $originalGroup */
            $originalGroup = $this->getOriginalGroup();
            $isTemplateChanged = $this->isTemplateChanged($originalGroup);
        }

        if ($isTemplateChanged) {
            $data = $this->filterData($data);
            /** @var \MageWorx\OptionTemplates\Model\Group $group */
            $group = $this->groupBuilder->build($this->getRequest());

            /**
             * Initialize product options
             */
            if (isset($data['options']) && !$group->getOptionsReadonly()) {
                $options = $this->mergeProductOptions(
                    $data['options'],
                    $this->_request->getPost('options_use_default')
                );
                $group->setProductOptions($options);
            }

            $group->addData($data);
            $group->setCanSaveCustomOptions(
                (bool)$group->getData('affect_product_custom_options') && !$group->getOptionsReadonly()
            );

            $currentGroup = $group;
        } else {
            $currentGroup = $originalGroup;
        }

        /**
         * Initialize product relation
         */
        $productIdsData = $this->getRequest()->getParam('group_products');
        if (is_null($productIdsData)) {
            $productIds = $currentGroup->getProducts();
        } else {
            $productIds = $this->getProductIds($productIdsData);
        }
        $productIds = $this->addProductsByIdSku($data, $productIds);
        $currentGroup->setProductsIds($productIds);

        $oldGroupCustomOptions = $currentGroup->getOptionArray();

        try {
            $this->registry->unregister('mageworx_optiontemplates_group_id');
            $this->registry->unregister('mageworx_optiontemplates_group_option_ids');

            if ($isTemplateChanged) {
                $this->_eventManager->dispatch(
                    'mageworx_optiontemplates_group_save_before',
                    [
                        'group' => $currentGroup,
                    ]
                );
                
		try {
                    $currentGroup->save();
                } catch(\Exception $exception) {
                    die("here!". $exception->getMessage());
                }               
		
		//e("here!");		

		$this->registry->register('mageworx_optiontemplates_group_id', $currentGroup->getId());
                $this->messageManager->addSuccessMessage(__('The options template has been saved.'));
                $this->_eventManager->dispatch(
                    'mageworx_optiontemplates_group_save_after',
                    [
                        'group' => $currentGroup,
                    ]
                );
                $this->optionSaver->saveProductOptions(
                    $currentGroup,
                    $oldGroupCustomOptions,
                    OptionSaver::SAVE_MODE_UPDATE
                );

            } else {
                if (!empty($data['title'])) {
                    $currentGroup->saveTitle($data['title']);
                }
                $this->registry->register('mageworx_optiontemplates_group_id', $currentGroup->getId());
                $this->messageManager->addSuccessMessage(__('The template has been modified.'));
                $currentGroup->setProductRelation(false);
                $this->optionSaver->saveProductOptions(
                    $currentGroup,
                    $oldGroupCustomOptions,
                    OptionSaver::SAVE_MODE_ADD_DELETE
                );
            }

            $this->_getSession()->setMageWorxOptionTemplatesGroupData(false);
            if ($redirectBack === 'duplicate') {
                $this->registry->unregister('mageworx_optiontemplates_group_id');
                $newGroup = $this->groupCopier->copy($currentGroup);
                $this->messageManager->addSuccessMessage(__('You duplicated the option template.'));
                $resultRedirect->setPath(
                    'mageworx_optiontemplates/*/edit',
                    ['group_id' => $newGroup->getId(), 'back' => null, '_current' => true]
                );
            }  elseif ($redirectBack === 'new') {
                $resultRedirect->setPath(
                    'mageworx_optiontemplates/*/edit'
                );
            } elseif ($redirectBack) {
                $resultRedirect->setPath(
                    'mageworx_optiontemplates/*/edit',
                    [
                        'group_id' => $currentGroup->getId(),
                        '_current' => true,
                    ]
                );
            } else {
                $resultRedirect->setPath('mageworx_optiontemplates/*/');
            }

            $this->registry->unregister('mageworx_optiontemplates_group_save');
            $this->registry->unregister('mageworx_optiontemplates_group_id');

            return $resultRedirect;
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the options template.'));
        } finally {
            $this->registry->unregister('mageworx_optiontemplates_group_save');
        }

        if ($redirectBack === 'duplicate' && isset($newGroup)) {
            $resultRedirect->setPath(
                'mageworx_optiontemplates/*/edit',
                ['group_id' => $newGroup->getId(), 'back' => null, '_current' => true]
            );
        } elseif ($redirectBack) {
            $resultRedirect->setPath(
                'mageworx_optiontemplates/*/edit',
                [
                    'group_id' => $currentGroup->getId(),
                    '_current' => true,
                ]
            );
        } else {
            $resultRedirect->setPath('mageworx_optiontemplates/*/');
        }
     
        } catch (\Exception $e) {
            
           die($e->getMessage());
     
        }    
        return $resultRedirect;
    }

    /**
     * Check if it is existing template or the new one
     *
     * @return bool
     */
    protected function isExistingTemplate()
    {
        return !empty($this->formData['group_id']) ? true : false;
    }

    /**
     * Get original group by group ID
     *
     * @return \MageWorx\OptionTemplates\Model\Group
     */
    protected function getOriginalGroup()
    {
        return $this->groupFactory->create()->load($this->formData['group_id']);
    }

    /**
     * Get original group by group ID
     *
     * @param \MageWorx\OptionTemplates\Model\Group $originalGroup
     * @return bool
     */
    protected function isGroupAttributesChanged($originalGroup)
    {
        $keys = [];
        $productAttributesKeys = [];
        $attributes = $this->productAttributes->getData();
        /** @var $attribute \MageWorx\OptionBase\Api\ProductAttributeInterface */
        foreach ($attributes as $attribute) {
            $productAttributesKeys[] = $attribute->getKeys();
        }
        foreach ($productAttributesKeys as $productAttributesKeyItems) {
            foreach ($productAttributesKeyItems as $productAttributesKey) {
                $keys[] = $productAttributesKey;
            }
        }
        foreach ($keys as $key) {
            if (isset($this->formData[$key]) && $originalGroup->getData($key) != $this->formData[$key]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Compare original template options with form template options
     *
     * @param \MageWorx\OptionTemplates\Model\Group\Option[] $originalGroupOptions
     * @return bool
     */
    protected function isOptionsChanged($originalGroupOptions)
    {
        $originalOptions = [];
        foreach ($originalGroupOptions as $option) {
            $option->setData('values', $option->getValues());
            $priceType = $option->getData('price_type');
            if (!isset($priceType)) {
                $option->setData('price_type', 'fixed');
            }
            $originalOptions[$option['mageworx_option_id']] = $option->getData();
        }
        $formOptions = [];
        foreach ($this->formData['options'] as $option) {
            if ($this->isNewOption($option) || $this->isDeleted($option)) {
                return true;
            }
            $formOptions[$option['mageworx_option_id']] = $option;
        }

        foreach ($originalOptions as $origOptionKey => $origOptionData) {
            if (empty($formOptions[$origOptionKey])) {
                return true;
            }
            foreach ($formOptions[$origOptionKey] as $formOptionKey => $formOptionData) {
                if (in_array($formOptionKey, ['option_id', 'record_id'])) {
                    continue;
                }
                if ($formOptionKey == 'values') {
                    if ($formOptionData && is_array($formOptionData) && isset($origOptionData['values'])) {
                        if ($this->isValuesChanged($origOptionData['values'], $formOptionData)) {
                            return true;
                        }
                    }
                } elseif (array_key_exists($formOptionKey, $origOptionData)
                    && $formOptionData != $origOptionData[$formOptionKey]
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for a new option
     *
     * @param array $option
     * @return bool
     */
    protected function isNewOption($option)
    {
        if (!isset($option['mageworx_option_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if option or value is deleted on form
     *
     * @param array $data
     * @return bool
     */
    protected function isDeleted($data)
    {
        if (isset($data['is_delete']) && $data['is_delete'] = 1) {
            return true;
        }
        return false;
    }

    /**
     * Compare original template option values with form template option values
     *
     * @param array $originalOptionValues
     * @param array $formOptionValues
     * @return bool
     */
    protected function isValuesChanged($originalOptionValues, $formOptionValues)
    {
        $originalValues = [];
        foreach ($originalOptionValues as $value) {
            $priceType = $value->getData('price_type');
            if (!isset($priceType)) {
                $value->setData('price_type', 'fixed');
            }
            $originalValues[$value['mageworx_option_type_id']] = $value->getData();
        }
        $formValues = [];
        foreach ($formOptionValues as $value) {
            if ($this->isNewValue($value) || $this->isDeleted($value)) {
                return true;
            }
            $formValues[$value['mageworx_option_type_id']] = $value;
        }

        foreach ($originalValues as $origValueKey => $origValueData) {
            if (empty($formValues[$origValueKey])) {
                return true;
            }
            foreach ($formValues[$origValueKey] as $formValueKey => $formValueData) {
                if (in_array($formValueKey, ['option_type_id', 'option_id', 'record_id'])) {
                    continue;
                }
                if (array_key_exists($formValueKey, $origValueData)
                    && $formValueData != $origValueData[$formValueKey]
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Check for a new option's value
     *
     * @param array $value
     * @return bool
     */
    protected function isNewValue($value)
    {
        if (!isset($value['mageworx_option_type_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Check if template is changed
     *
     * @param \MageWorx\OptionTemplates\Model\Group\ $originalGroup
     * @return bool
     */
    protected function isTemplateChanged($originalGroup)
    {
        if (!$originalGroup) {
            return true;
        }
        if (count($originalGroup->getOptions()) != count($this->formData['options'])) {
            return true;
        }
        if ($this->isGroupAttributesChanged($originalGroup)) {
            return true;
        }
        return $this->isOptionsChanged($originalGroup->getOptions());
    }

    /**
     * Merge product and default options for product
     *
     * @param array $productOptions product options
     * @param array $overwriteOptions default value options
     * @return array
     */
    protected function mergeProductOptions($productOptions, $overwriteOptions)
    {
        if (!is_array($productOptions)) {
            $productOptions = [];
        }
        if (is_array($overwriteOptions)) {
            $options = array_replace_recursive($productOptions, $overwriteOptions);
            array_walk_recursive($options, function (&$item) {
                if ($item === "") {
                    $item = null;
                }
            });
        } else {
            $options = $productOptions;
        }

        return $options;
    }

    /**
     *
     * @param string $data
     * @return array
     */
    protected function getProductIds($data)
    {
        if (!empty($data)) {
            $productIds = json_decode($data, true);
        } else {
            $productIds = [];
        }

        return $productIds;
    }

    /**
     *
     * @param array $data
     * @param array $assignedProductIds
     * @return array
     */
    protected function addProductsByIdSku($data, $assignedProductIds)
    {
        $productIds = [];

        if ($data['assign_type'] == AssignType::ASSIGN_BY_GRID) {
            return $assignedProductIds;
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_IDS) {
            $productIds = $this->convertMultiStringToArray($data['productids'], 'intval');
        } elseif ($data['assign_type'] == AssignType::ASSIGN_BY_SKUS) {
            $productSkus = $this->convertMultiStringToArray($data['productskus']);

            if ($productSkus) {
                /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
                $collection = $this->productCollectionFactory->create();
                $collection->addFieldToFilter('sku', ['in' => $productSkus]);
                $productIds = array_map('intval', $collection->getAllIds());
            }
        }

        return array_merge($assignedProductIds, $productIds);
    }

    /**
     *
     * @param string $string
     * @param string $finalFunction
     * @return array
     */
    protected function convertMultiStringToArray($string, $finalFunction = null)
    {
        if (!trim($string)) {
            return [];
        }

        $rawLines = array_filter(preg_split('/\r?\n/', $string));
        $rawLines = array_map('trim', $rawLines);
        $lines = array_filter($rawLines);

        if (!$lines) {
            return [];
        }

        $array = [];
        foreach ($lines as $line) {
            $rawIds = explode(',', $line);
            $rawIds = array_map('trim', $rawIds);
            $lineIds = array_filter($rawIds);
            if (!$finalFunction) {
                $lineIds = array_map($finalFunction, $lineIds);
            }
            $array = array_merge($array, $lineIds);
        }

        return $array;
    }

    protected function filterData($data)
    {
        if (isset($data['group_id']) && !$data['group_id']) {
            unset($data['group_id']);
        }

        if (isset($data['options'])) {
            $updatedOptions = [];
            foreach ($data['options'] as $key => $option) {
                if (!isset($option['option_id'])) {
                    continue;
                }

                $optionId = $option['option_id'];
                if (!$optionId && !empty($option['record_id'])) {
                    $optionId = $option['record_id'] . '_';
                }
                $updatedOptions[$optionId] = $option;
                if (empty($option['values'])) {
                    continue;
                }

                $values = $option['values'];
                foreach ($option['values'] as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    unset($updatedOptions[$optionId]['values'][$valueKey]);
                }
                foreach ($values as $valueKey => $value) {
                    if (!isset($value['option_type_id'])) {
                        continue;
                    }
                    $valueId = $value['option_type_id'];
                    $updatedOptions[$optionId]['values'][$valueId] = $value;
                }
            }

            $data['options'] = $updatedOptions;
        }

        return $data;
    }
}
