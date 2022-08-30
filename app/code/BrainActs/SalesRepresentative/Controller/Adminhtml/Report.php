<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml;

/**
 * @SuppressWarnings(PHPMD.NumberOfChildren)
 */
abstract class Report extends AbstractReport
{
    /**
     * Add report/sales breadcrumbs
     *
     * @return $this
     */
    public function _initAction()
    {
        parent::_initAction();
        $this->_addBreadcrumb(__('Sales'), __('Sales'));
        return $this;
    }

    /**
     * Determine if action is allowed for reports module
     *
     * @return bool
     */
//    protected function _isAllowed()
//    {
//        switch ($this->getRequest()->getActionName()) {
//            case 'sales':
//                return $this->_authorization->isAllowed('Magento_Reports::salesroot_sales');
//                break;
//            case 'tax':
//                return $this->_authorization->isAllowed('Magento_Reports::tax');
//                break;
//            case 'shipping':
//                return $this->_authorization->isAllowed('Magento_Reports::shipping');
//                break;
//            case 'invoiced':
//                return $this->_authorization->isAllowed('Magento_Reports::invoiced');
//                break;
//            case 'refunded':
//                return $this->_authorization->isAllowed('Magento_Reports::refunded');
//                break;
//            case 'coupons':
//                return $this->_authorization->isAllowed('Magento_Reports::coupons');
//                break;
//            case 'shipping':
//                return $this->_authorization->isAllowed('Magento_Reports::shipping');
//                break;
//            case 'bestsellers':
//                return $this->_authorization->isAllowed('Magento_Reports::bestsellers');
//                break;
//            default:
//                return $this->_authorization->isAllowed('Magento_Reports::salesroot');
//                break;
//        }
//    }
}
