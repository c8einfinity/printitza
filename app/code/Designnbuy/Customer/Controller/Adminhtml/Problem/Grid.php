<?php
/**
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Controller\Adminhtml\Problem;

class Grid extends \Designnbuy\Customer\Controller\Adminhtml\Problem
{
    /**
     * Customer problems grid
     *
     * @return void
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('_unsubscribe')) {
            $problems = (array)$this->getRequest()->getParam('problem', []);
            if (count($problems) > 0) {
                $collection =
                    $this->_objectManager->create('Designnbuy\Customer\Model\ResourceModel\Problem\Collection');
                $collection->addDesignInfo()->addFieldToFilter(
                    $collection->getResource()->getIdFieldName(),
                    ['in' => $problems]
                )->load();

                $collection->walk('unsubscribe');
            }

            $this->messageManager->addSuccess(__('We unsubscribed the people you identified.'));
        }

        if ($this->getRequest()->getParam('_delete')) {
            $problems = (array)$this->getRequest()->getParam('problem', []);
            if (count($problems) > 0) {
                $collection =
                    $this->_objectManager->create('Designnbuy\Customer\Model\ResourceModel\Problem\Collection');
                $collection->addFieldToFilter(
                    $collection->getResource()->getIdFieldName(),
                    ['in' => $problems]
                )->load();
                $collection->walk('delete');
            }

            $this->messageManager->addSuccess(__('The problems you identified have been deleted.'));
        }
        $this->_view->loadLayout(false);
        $this->_view->getLayout()->getMessagesBlock()->setMessages($this->messageManager->getMessages(true));
        $this->_view->renderLayout();
    }
}
