<?php

namespace Designnbuy\Template\Block\Adminhtml\Category\Edit\Tab;

//use Designnbuy\Template\Model\Status;
//use Designnbuy\Template\Model\Category;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;
use Magento\Cms\Model\Wysiwyg\Config;
/**
 * Template Form.
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
//class Form extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
class Form extends \Magento\Catalog\Block\Adminhtml\Form implements \Magento\Backend\Block\Widget\Tab\TabInterface
//class Form extends \Magento\Catalog\Block\Adminhtml\Form
{
    const FIELD_NAME = 'category';

    /**
     * @var \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory
     */
    protected $_fieldFactory;

    /**
     * [$_bannertemplateHelper description].
     *
     * @var \Designnbuy\Template\Helper\Data
     */
    protected $_bannertemplateHelper;

    /**
     * available status.
     *
     * @var \Designnbuy\Template\Model\Status
     */
    private $_status;
    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;
    /**
     * [__construct description].
     *
     * @param \Magento\Backend\Block\Template\Context                                $context
     * //@param \Designnbuy\Template\Helper\Data                               $bannertemplateHelper
     * @param \Magento\Framework\Registry                                            $registry
     * @param \Magento\Framework\Data\FormFactory                                    $formFactory
     * @param \Magento\Store\Model\System\Store                                      $systemStore
     * @param \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory     *
     * @param array                                                                  $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        //\Designnbuy\Template\Helper\Data $bannertemplateHelper,
        \Magento\Config\Model\Config\Structure\Element\Dependency\FieldFactory $fieldFactory,
        \Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_fieldFactory       = $fieldFactory;
        $this->eavConfig = $eavConfig;
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());
        \Magento\Framework\Data\Form::setElementRenderer(
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Renderer\Element',
                $this->getNameInLayout() . '_element'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetRenderer(
            $this->getLayout()->createBlock(
                'Magento\Backend\Block\Widget\Form\Renderer\Fieldset',
                $this->getNameInLayout() . '_fieldset'
            )
        );
        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'Designnbuy\Template\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout() . '_fieldset_element'
            )
        );
    }
    /**
     * Additional element types for product attributes
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'image' => 'Designnbuy\Template\Block\Adminhtml\Category\Helper\Image'
        ];
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $template = $this->getCurrentCategory();

        $isElementDisabled = true;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setDataObject($template);

        $elements = [];

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Category Details')]);

        $attributes = $this->getAttributes();

        $entity = $this->getEavConfig()->getEntityType(\Designnbuy\Template\Model\Category::ENTITY)->getEntity();
            foreach ($attributes as $attribute) {
                $attribute->setEntity($entity);
            }

        $this->_setFieldset($attributes, $fieldset, array());

        $formValues = $template->getData();
        
        if (!$template->getEntityId()) {
            foreach ($attributes as $attribute) {
                if (!isset($formValues[$attribute->getAttributeCode()])) {
                    $formValues[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                }
            }
        }

        if ($template->getEntityId()) {
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        }

        /*$fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => __('Title'),
                'title'    => __('Title'),
                'required' => true,
                'class'    => 'required-entry'
            ]
        );*/

        $form->addValues($formValues);
        $form->setValues($formValues);
        $form->setFieldNameSuffix(self::FIELD_NAME);
        $this->setForm($form);

        return parent::_prepareForm();
    }
    /**
     * Retrieve EAV Config Singleton
     *
     * @return \Magento\Eav\Model\Config
     */
    private function getEavConfig()
    {
        return $this->eavConfig;
    }
    /**
     * @return void
     */

    public function getCurrentCategory()
    {
        return $this->_coreRegistry->registry('current_category');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Category');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Category');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return true;
    }


}
