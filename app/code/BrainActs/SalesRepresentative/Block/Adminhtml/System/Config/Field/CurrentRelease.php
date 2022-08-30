<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class CurrentRelease
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class CurrentRelease extends Field
{

    /**
     * @var \BrainActs\Hub\Model\ExtensionFactory
     */
    private $extensionFactory;

    /**
     * @var \Magento\Framework\Module\ModuleList
     */
    private $moduleList;

    /**
     * CurrentRelease constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \BrainActs\Hub\Model\ExtensionFactory $extensionFactory
     * @param \Magento\Framework\Module\ModuleList $moduleList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \BrainActs\Hub\Model\ExtensionFactory $extensionFactory,
        \Magento\Framework\Module\ModuleList $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->extensionFactory = $extensionFactory;
        $this->moduleList = $moduleList;
    }

    /**
     * @param AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element)//@codingStandardsIgnoreLine
    {
        $moduleName = $this->getModuleName();

        $hub = $this->extensionFactory->create();
        $info = $hub->getExtensionInfo($moduleName);

        $data = $this->moduleList->getOne($this->getModuleName());

        $class = 'current-release';
        if (version_compare($data['setup_version'], $info->getVersion())) {
            $class .= ' warning-release';
        }

        return '<p class="' . $class . '">' . $this->escapeHtml($info->getVersion()) . '</p>';
    }

    /**
     * @param AbstractElement $element
     * @return bool
     */
    protected function _isInheritCheckboxRequired(AbstractElement $element)//@codingStandardsIgnoreLine
    {
        return false;
    }
}
