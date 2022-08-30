<?php

/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Adminhtml\Customer;

use BrainActs\SalesRepresentative\Model\ResourceModel\MemberFactory;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Approve
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Approve extends \Magento\Backend\App\Action
{

    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'BrainActs_SalesRepresentative::sales_representative_member';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var MemberFactory
     */
    private $resourceMemberFactory;

    /**
     * Approve constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     * @param MemberFactory $resourceMemberFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        PageFactory $resultPageFactory,
        MemberFactory $resourceMemberFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->resourceMemberFactory = $resourceMemberFactory;

        parent::__construct($context);
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $confirmRequest = $this->initChangeRequest();
        if (!$confirmRequest) {
            $this->messageManager->addError(__('Link to confirm change SR for customer no longer exist.'));
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->getUrl('salesrep/customer'))
            );
        }

        $members = unserialize($confirmRequest['to']);

        $resource = $this->resourceMemberFactory->create();

        //remove customer from other members
        $resource->removeMembersFromCustomer($confirmRequest['customer_id']);

        //assign to member
        foreach ($members as $memberId) {
            $resource->applyMemberToCustomer($confirmRequest['customer_id'], $memberId);
        }

        $resource->removeConfirmationRequest($confirmRequest['customer_id']);

        $this->messageManager->addSuccess(__('Request to changes representative for customer has been approved.'));

        return $this->resultRedirectFactory->create()->setUrl(
            $this->_redirect->error($this->getUrl('salesrep/customer'))
        );
    }

    private function initChangeRequest()
    {
        $id = $this->_request->getParam('id', false);

        if (!$id) {
            return false;
        }

        /** @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member $resource */
        $resource = $this->resourceMemberFactory->create();
        $confirmRequest = $resource->getRequestById($id);
        if (empty($confirmRequest)) {
            return false;
        }

        return $confirmRequest;
    }
}
