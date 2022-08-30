<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * General Tab in New ORDERTICKET form
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\NewOrderTicket\Tab;

/**
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class General extends \Designnbuy\OrderTicket\Block\Adminhtml\OrderTicket\Edit\Tab\General
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

        $this->setForm($form);
        return $this;
    }
}
