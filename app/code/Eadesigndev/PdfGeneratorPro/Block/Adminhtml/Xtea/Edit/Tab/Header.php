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
 * Class Header
 * @package Eadesigndev\PdfGeneratorPro\Block\Adminhtml\Xtea\Edit\Tab
 */
class Header extends Generic implements TabInterface
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

        $form->setHtmlIdPrefix('header_');

        $fieldSet = $form->addFieldset('base_fieldset', ['legend' => __('Settings')]);

        if ($model->getId()) {
            $fieldSet->addField('template_id', 'hidden', ['name' => 'template_id']);
        }

        $fieldSet->addField(
            'variables_entity_id',
            'text',
            [
                'name' => 'variables_entity_id',
                'label' => __('Source id for variables'),
                'title' => __('Source id for variables'),
                'required' => false,
                'after_element_html' => __(
                    'Here you need to add the increment id of the source for you want to use the template for.
                    This will be used for all the variables from the body and footer.'
                ),
            ]
        );

        $editor = $fieldSet->addField('template_header', 'editor', [
            'name' => 'template_header',
            'label' => '',
            'config' => $this->processConfig(),
            'wysiwyg' => true,
            'required' => false,
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
        return __('Html Header');
    }

    /**
     * Prepare title for tab
     *
     * @return Phrase
     */
    public function getTabTitle()
    {
        return __('Html Header');
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
