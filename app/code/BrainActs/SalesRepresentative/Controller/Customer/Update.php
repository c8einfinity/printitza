<?php
/**
 * Copyright © BrainActs Commerce OÜ. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace BrainActs\SalesRepresentative\Controller\Customer;

use BrainActs\SalesRepresentative\Model\ResourceModel\Member\CollectionFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;

/**
 * Class Update
 * @author BrainActs Commerce OÜ Core Team <support@brainacts.com>
 */
class Update extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    private $formKeyValidator;

    /**
     * @var \BrainActs\SalesRepresentative\Model\ResourceModel\Member
     */
    private $memberResource;

    /**
     * @var CollectionFactory
     */
    private $memberResourceCollectionFactory;

    /**
     * @var \BrainActs\SalesRepresentative\Model\Config
     */
    private $config;

    /**
     * @var \BrainActs\SalesRepresentative\Helper\Email
     */
    private $emailHelper;

    /**
     * @var \Magento\Backend\Model\Url
     */
    private $backendUrl;

    /**
     * Update constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \BrainActs\SalesRepresentative\Model\ResourceModel\Member $memberResource
     * @param CollectionFactory $memberResourceCollectionFactory
     * @param \BrainActs\SalesRepresentative\Model\Config $config
     * @param \BrainActs\SalesRepresentative\Helper\Email $emailHelper
     * @param \Magento\Backend\Model\Url $backendUrl
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \BrainActs\SalesRepresentative\Model\ResourceModel\Member $memberResource,
        CollectionFactory $memberResourceCollectionFactory,
        \BrainActs\SalesRepresentative\Model\Config $config,
        \BrainActs\SalesRepresentative\Helper\Email $emailHelper,
        \Magento\Backend\Model\Url $backendUrl
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        $this->customerSession = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->memberResource = $memberResource;
        $this->memberResourceCollectionFactory = $memberResourceCollectionFactory;
        $this->config = $config;
        $this->emailHelper = $emailHelper;
        $this->backendUrl = $backendUrl;

        parent::__construct($context);
    }

    /**
     * Check customer authentication
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function dispatch(RequestInterface $request)
    {
        if (!$this->getSession()->authenticate()) {
            $this->_actionFlag->set('', 'no-dispatch', true);
        }
        return parent::dispatch($request);
    }

    /**
     * Retrieve customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    private function getSession()
    {
        return $this->customerSession;
    }

    /**
     * Render View
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $redirectUrl = null;
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('customer/account/');
        }

        if (!$this->getRequest()->isPost()) {
            return $this->resultRedirectFactory->create()->setUrl(
                $this->_redirect->error($this->_url->getUrl('customer/account/'))
            );
        }

        try {
            $members = $this->_request->getParam('members', []);
            $customer = $this->getSession()->getCustomer();

            if (!$this->isConfirm()) {
                //remove customer from other members
                $this->memberResource->removeMembersFromCustomer($customer->getId());

                //assign to member
                foreach ($members as $memberId) {
                    $this->memberResource->applyMemberToCustomer($customer->getId(), $memberId);
                }

                $this->messageManager->addSuccess(__('You updated the manager settings.'));
            } else {
                //create record about confirmation and send email
                $currentMembers = $this->getCurrentMembers($customer->getId(), true);

                $id = $this->memberResource
                    ->saveConfirmationRequest($customer->getId(), serialize($members), serialize($currentMembers));

                $vars = [
                    'customerName' => $customer->getName(),
                    'adminUrl' => $this->backendUrl->getUrl('admin'),
                    'oldMembers' => $this->getCurrentMembers($customer->getId()),
                    'newMembers' => $this->getNewMembers($members),

                ];

                $block = $this->prepareBlockVars($vars['newMembers'], $vars['oldMembers']);
                $vars[$block] = true;

                $this->emailHelper->sendConfirmChangeMember($vars);
            }

            $url = $this->_url->getUrl('customer/account/', ['_secure' => true]);
            return $this->resultRedirectFactory->create()->setUrl($this->_redirect->success($url));
        } catch (\Exception $e) {
            $redirectUrl = $this->_url->getUrl('customer/account/');
            $this->messageManager->addException($e, __('We can\'t update the managers.'));
        }

        $url = $redirectUrl;

        if (!$url) {
            $url = $this->_url->getUrl('customer/account/');
        }

        return $this->resultRedirectFactory->create()->setUrl($this->_redirect->error($url));
    }

    /**
     * @param string $new
     * @param string $old
     * @return string
     */
    private function prepareBlockVars($new, $old)
    {

        if (!empty($new) && !empty($old)) {
            return 'replace';
        }
        if (!empty($new) && empty($old)) {
            return 'new';
        }

        if (empty($new) && !empty($old)) {
            return 'remove';
        }
    }

    /**
     * Return status of confirmation
     * @return bool
     */
    private function isConfirm()
    {
        return $this->config->adminConfirmChange();
    }

    /**
     * @param $customerId
     * @param bool $ids
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCurrentMembers($customerId, $ids = false)
    {
        $members = $this->memberResource
            ->getMembersNamesByCustomer($customerId, ['member_id', 'firstname', 'lastname']);

        if ($ids === false) {
            $names = [];
            foreach ($members as $member) {
                $names [] = $member['firstname'] . ' ' . $member['lastname'];
            }
            return implode(', ', $names);
        }

        $ids = [];
        foreach ($members as $member) {
            $ids [] = $member['member_id'];
        }
        return $ids;
    }

    /**
     * Get members by ids
     * @param $membersIds
     * @return string
     */
    private function getNewMembers($membersIds)
    {

        $collection = [];

        if (!empty($membersIds)) {
            $collection = $this->memberResourceCollectionFactory->create();
            $collection->addFieldToFilter('member_id', ['in' => $membersIds]);
        }

        $names = [];
        foreach ($collection as $member) {
            $names [] = $member->getFirstname() . ' ' . $member->getLastname();
        }
        return implode(', ', $names);
    }
}
