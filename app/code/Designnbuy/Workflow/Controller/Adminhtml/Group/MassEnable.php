<?php
/**
 *
 * Copyright © 2015 Designnbuy. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Designnbuy\Workflow\Controller\Adminhtml\Group;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Designnbuy\Workflow\Model\ResourceModel\Group\CollectionFactory;
use Magento\Backend\App\Action;

class MassEnable extends Action
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

        foreach ($collection as $group) {
            $group->setStatus(true);
            $group->save();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been enable.', $collectionSize));

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
