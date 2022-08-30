<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Customer template preview block
 */
namespace Designnbuy\Customer\Block\Adminhtml\Template;

class Preview extends \Magento\Backend\Block\Widget
{
    /**
     * Name for profiler
     *
     * @var string
     */
    protected $profilerName = "customer_template_proccessing";

    /**
     * @var \Designnbuy\Customer\Model\TemplateFactory
     */
    protected $_templateFactory;

    /**
     * @var \Designnbuy\Customer\Model\DesignFactory
     */
    protected $_designFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Designnbuy\Customer\Model\TemplateFactory $templateFactory
     * @param \Designnbuy\Customer\Model\DesignFactory $designFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Designnbuy\Customer\Model\TemplateFactory $templateFactory,
        \Designnbuy\Customer\Model\DesignFactory $designFactory,
        array $data = []
    ) {
        $this->_templateFactory = $templateFactory;
        $this->_designFactory = $designFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get html code
     *
     * @return string
     */
    protected function _toHtml()
    {
        /* @var $template \Designnbuy\Customer\Model\Template */
        $template = $this->_templateFactory->create();

        if ($id = (int)$this->getRequest()->getParam('id')) {
            $this->loadTemplate($template, $id);
        } else {
            $previewData = $this->getPreviewData();

            $template->setTemplateType($previewData['type']);
            $template->setTemplateText($previewData['text']);
            $template->setTemplateStyles($previewData['styles']);
        }

        \Magento\Framework\Profiler::start($this->profilerName);
        $vars = [];

        $vars['design'] = $this->_designFactory->create();
        if ($this->getRequest()->getParam('design')) {
            $vars['design']->load($this->getRequest()->getParam('design'));
        }

        $template->emulateDesign($this->getStoreId());
        $templateProcessed = $this->_appState->emulateAreaCode(
            \Designnbuy\Customer\Model\Template::DEFAULT_DESIGN_AREA,
            [$template, 'getProcessedTemplate'],
            [$vars]
        );
        $template->revertDesign();

        if ($template->isPlain()) {
            $templateProcessed = "<pre>" . htmlspecialchars($templateProcessed) . "</pre>";
        }

        \Magento\Framework\Profiler::stop($this->profilerName);

        return $templateProcessed;
    }

    /**
     * Return template preview data
     *
     * @return array
     */
    private function getPreviewData()
    {
        $previewData = [];
        $previewParams = ['type', 'text', 'styles'];

        $sessionData = [];
        if ($this->_backendSession->hasPreviewData()) {
            $sessionData = $this->_backendSession->getPreviewData();
        }

        foreach ($previewParams as $param) {
            if (isset($sessionData[$param])) {
                $previewData[$param] = $sessionData[$param];
            } else {
                $previewData[$param] = $this->getRequest()->getParam($param);
            }
        }

        return $previewData;
    }

    /**
     * Get Store Id from request or default
     *
     * @return int|null
     */
    protected function getStoreId()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        if ($storeId) {
            return $storeId;
        }

        $defaultStore = $this->_storeManager->getDefaultStoreView();
        if (!$defaultStore) {
            $allStores = $this->_storeManager->getStores();
            if (isset($allStores[0])) {
                $defaultStore = $allStores[0];
            }
        }

        return $defaultStore ? $defaultStore->getId() : null;
    }

    /**
     * @param \Designnbuy\Customer\Model\Template $template
     * @param string $id
     * @return $this
     */
    protected function loadTemplate(\Designnbuy\Customer\Model\Template $template, $id)
    {
        $template->load($id);
        return $this;
    }
}
