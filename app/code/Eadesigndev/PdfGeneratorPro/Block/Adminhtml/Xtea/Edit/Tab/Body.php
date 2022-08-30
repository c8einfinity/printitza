<?php

namespace Eadesigndev\PdfGeneratorPro\Block\Adminhtml\Xtea\Edit\Tab;

use Eadesigndev\PdfGeneratorPro\Block\Adminhtml\Xtea\Edit\Renderer\Editor;
use Eadesigndev\PdfGeneratorPro\Model\Pdfgenerator;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Model\UrlInterface as ButtonsVariable;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;

/**
 * Class Body
 * @package Eadesigndev\PdfGeneratorPro\Block\Adminhtml\Xtea\Edit\Tab
 */
class Body extends Generic implements TabInterface
{

    /**
     * @var WysiwygConfig
     */
    private $wysiwygConfig;

    /**
     * @var ButtonsVariable
     */
    private $buttonsVariable;

    /**
     * Body constructor.
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param WysiwygConfig $wysiwygConfig
     * @param ButtonsVariable $buttonsVariable
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        WysiwygConfig $wysiwygConfig,
        ButtonsVariable $buttonsVariable,
        array $data = []
    ) {
        $this->wysiwygConfig   = $wysiwygConfig;
        $this->buttonsVariable = $buttonsVariable;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    public function _prepareForm()
    {

        /** @var Pdfgenerator $model */
        $model = $this->_coreRegistry->registry('pdfgenerator_template');

        /** @var Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('body_');

        $fieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Settings')]);

        if ($model->getId()) {
            $fieldSet->addField('template_id', 'hidden', ['name' => 'template_id']);
        }

        $editor = $fieldSet->addField('template_body', 'editor', [
            'name' => 'template_body',
            'label' => '',
            'config' => $this->processConfig(),
            'wysiwyg' => true,
            'required' => true,
            'after_element_html' => __('Use of editor might create issues.'),
        ]);

        $renderer = $this->getLayout()->createBlock(
            Editor::class
        );
        $editor->setRenderer($renderer);

        $form->setValues($model->getData());
        $this->setForm($form);

        parent::_prepareForm();

        return $this;
    }

    /**
     * @return Phrase
     */
    public function getTabLabel()
    {
        return __('Html Body');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Html Body');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return \Magento\Framework\DataObject
     */
    private function processConfig()
    {
        $data = [
            'hidden' => false,
            'add_variables' => false,
            'add_widgets' => false,
            'add_images' => false

        ];

        $config = $this->wysiwygConfig->getConfig($data);

        return $config;
    }
}
