<?php
/**
 *
 * Copyright © 2015 Designnbuy. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Designnbuy\Workflow\Controller\Adminhtml\User;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Designnbuy\Workflow\Model\ResourceModel\User\CollectionFactory;
use Magento\Backend\App\Action;

class MassDisable extends Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * @return $this
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $user) {
            $user->setStatus(false);
            $user->save();
            $userId = $user->getUserId();
            $this->changeUserStatus($userId, 0);
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been disable.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }

    public function changeUserStatus($userId, $status) {
        $data = ['is_active' => $status];
        $this->_resources = \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Framework\App\ResourceConnection');
        $this->_resources->getConnection()->update(
            'admin_user',
            $data,
            ['user_id = ?' => (int)$userId]
        );
    }
}
