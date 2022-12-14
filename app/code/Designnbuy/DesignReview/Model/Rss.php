<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\DesignReview\Model;

/**
 * Class Rss
 * @package Magento\Catalog\Model\Rss\Product
 */
class Rss extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Designnbuy\DesignReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Designnbuy\DesignReview\Model\ReviewFactory $reviewFactory
    ) {
        $this->reviewFactory = $reviewFactory;
        $this->eventManager = $eventManager;
    }

    /**
     * @return $this|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getProductCollection()
    {
        /** @var $reviewModel \Designnbuy\DesignReview\Model\Review */
        $reviewModel = $this->reviewFactory->create();
        $collection = $reviewModel->getProductCollection()
            ->addStatusFilter($reviewModel->getPendingStatus())
            ->addAttributeToSelect('name', 'inner')
            ->setDateOrder();

        $this->eventManager->dispatch('rss_catalog_review_collection_select', ['collection' => $collection]);
        return $collection;
    }
}
