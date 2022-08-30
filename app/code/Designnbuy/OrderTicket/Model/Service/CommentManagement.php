<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\Service;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Designnbuy\OrderTicket\Model\OrderTicket\PermissionChecker;
use Designnbuy\OrderTicket\Api\CommentRepositoryInterface;
use Designnbuy\OrderTicket\Api\OrderTicketRepositoryInterface;

/**
 * Class CommentManagement
 */
class CommentManagement implements \Designnbuy\OrderTicket\Api\CommentManagementInterface
{
    /**
     * @var CommentRepositoryInterface
     */
    protected $commentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;

    /**
     * @var PermissionChecker
     */
    private $permissionChecker;

    /**
     * @var OrderTicketRepositoryInterface
     */
    private $repository;

    /**
     * @param CommentRepositoryInterface $commentRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param FilterBuilder $filterBuilder
     * @param OrderTicketRepositoryInterface $repository
     * @param PermissionChecker $permissionChecker
     */
    public function __construct(
        CommentRepositoryInterface $commentRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        FilterBuilder $filterBuilder,
        OrderTicketRepositoryInterface $repository,
        PermissionChecker $permissionChecker
    ) {
        $this->commentRepository = $commentRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->permissionChecker = $permissionChecker;
        $this->repository = $repository;
    }

    /**
     * Add comment
     *
     * @param \Designnbuy\OrderTicket\Api\Data\CommentInterface $comment
     * @return bool
     * @throws \Exception
     */
    public function addComment(\Designnbuy\OrderTicket\Api\Data\CommentInterface $comment)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkOrderTicketForCustomerContext();

        $message = trim($comment->getComment());
        if (!$message) {
            throw new \Magento\Framework\Exception\InputException(__('Please enter a valid comment.'));
        }

        if ($comment->getIsCustomerNotified()) {
            $comment->sendCustomerCommentEmail();
        }
        $comment->setIsAdmin(true);
        $this->commentRepository->save($comment);
        return true;
    }

    /**
     * Comments list
     *
     * @param int $id
     * @return \Designnbuy\OrderTicket\Api\Data\CommentSearchResultInterface
     */
    public function commentsList($id)
    {
        /** @todo Find a way to place this logic somewhere else(not to plugins!) */
        $this->permissionChecker->checkOrderTicketForCustomerContext();

        $orderticketModel = $this->repository->get($id);

        $filters = [$this->filterBuilder->setField('orderticket_entity_id')->setValue($orderticketModel->getEntityId())->create()];
        if ($this->permissionChecker->isCustomerContext()) {
            $filters[] = $this->filterBuilder->setField('is_visible_on_front')->setValue(1)->create();
        }

        $this->criteriaBuilder->addFilters($filters);

        $criteria = $this->criteriaBuilder->create();
        return $this->commentRepository->getList($criteria);
    }
}
