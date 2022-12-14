<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * General Tab in Edit ORDERTICKET form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab;

class General extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Create form. Fieldset are being added in child blocks
     *
     * @return \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $htmlIdPrefix = 'orderticket_properties_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = $this->_coreRegistry->registry('current_orderticket');

        if ($model) {
            $form->setValues($model->getData());
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
