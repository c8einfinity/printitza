<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket;

class AddProductGrid extends \Designnbuy\OrderTicket\Controller\Adminhtml\OrderTicket
{
    /**
     * Generate ORDERTICKET items grid for ajax request from selecting product grid during ORDERTICKET creation
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        try {
            $this->_initModel();
            $order = $this->_coreRegistry->registry('current_order');
            if (!$order) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid order'));
            }
            $this->_view->loadLayout();
            $response = $this->_view->getLayout()->getBlock('add_product_grid')->toHtml();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $response = ['error' => true, 'message' => $e->getMessage()];
        } catch (\Exception $e) {
            $response = ['error' => true, 'message' => __('We can\'t retrieve the product list right now.')];
        }
        if (is_array($response)) {
            $response = $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response);
            $this->getResponse()->representJson($response);
        } else {
            $this->getResponse()->setBody($response);
        }
    }
}
