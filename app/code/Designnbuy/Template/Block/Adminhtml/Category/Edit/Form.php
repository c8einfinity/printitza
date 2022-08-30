<?php

namespace Designnbuy\Template\Block\Adminhtml\Category\Edit;

/**
 * Adminhtml locator edit form block.
 * @category Designnbuy
 * @package  Designnbuy_Template
 * @module   Template
 * @author   Designnbuy Developer
 */
class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id'      => 'edit_form',
                    'action'  => $this->getUrl(
                        '*/*/save',
                        [
                            'id' => $this->getRequest()->getParam('id'),
                            'store' => $this->getRequest()->getParam('store')
                        ]
                    ),
                    'method'  => 'post',
                    'enctype' => 'multipart/form-data',
                ],
            ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
