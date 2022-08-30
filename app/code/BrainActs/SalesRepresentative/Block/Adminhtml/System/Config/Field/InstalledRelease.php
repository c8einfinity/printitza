<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Module\ModuleList;

/**
 * Class InstalledRelease
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class InstalledRelease extends Field
{

    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * InstalledRelease constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param ModuleList $moduleList
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Module\ModuleList $moduleList,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleList = $moduleList;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)//@codingStandardsIgnoreLine
    {
        $data = $this->moduleList->getOne($this->getModuleName());
        return '<p class="installed-release">' . $this->escapeHtml($data['setup_version']) . '</p>';
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
