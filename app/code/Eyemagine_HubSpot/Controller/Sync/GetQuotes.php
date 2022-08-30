<?php

/**
 * EYEMAGINE - The leading Magento Solution Partner
 *
 * HubSpot Integration with Magento
 *
 * @author    EYEMAGINE <magento@eyemaginetech.com>
 * @copyright Copyright (c) 2016 EYEMAGINE Technology, LLC (http://www.eyemaginetech.com)
 * @license   http://www.eyemaginetech.com/license
 */
namespace Eyemagine\HubSpot\Controller\Sync;

use Magento\Framework\App\Action\Context;
use Eyemagine\HubSpot\Controller\AbstractSync;
use Magento\Framework\Controller\Result\JsonFactory;
use Eyemagine\HubSpot\Helper\Sync as HelperSync;
use Exception;

/**
 * Class GetQuotes
 *
 * @package Eyemagine\HubSpot\Controller\Sync
 */
class GetQuotes extends AbstractSync
{

    /**
     *
     * @var \Eyemagine\HubSpot\Helper\Sync
     */
    protected $helper;

    protected $resource;

    /**
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Eyemagine\HubSpot\Helper\Sync $helperSync
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        HelperSync $helperSync,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        parent::__construct($context, $resultJsonFactory);

        $this->helper = $helperSync;
        $this->resource = $resource;
    }

    /**
     * Get abandoned cart data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try {
            if (! $this->helper->authenticate()) {
                return $this->outputError($this->helper->getErrorCode(), $this->helper->getErrorMessage(), null);
            }

            $request = $this->getRequest();
            $multistore = $request->getParam('multistore', self::IS_MULTISTORE);
            $maxperpage = $request->getParam('maxperpage', self::MAX_ORDER_PERPAGE);
            $maxAssociated = $request->getParam('maxassoc', self::MAX_ASSOC_PRODUCT_LIMIT);
            $start = gmdate('Y-m-d H:i:s', $request->getParam('start', 0));
            $end = gmdate('Y-m-d H:i:s', time() - 300);
            $websiteId = $this->helper->getWebsiteId();
            $storeId = $this->helper->getStoreId();
            $custGroups = $this->helper->getCustomerGroups();
            $returnData = array();
            $storeCode = $this->helper->getStoreCode();
            $stores = $this->helper->getStores();

            if (class_exists('\Cart2Quote\Quotation\Model\Quote')) {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

                $quoteRepository = $objectManager->create('\Cart2Quote\Quotation\Api\QuoteRepositoryInterface');
                $searchCriteriaBuilder = $objectManager->create('\Magento\Framework\Api\SearchCriteriaBuilder');
                $filterBuilder = $objectManager->create('\Magento\Framework\Api\FilterBuilder');
                $sortOrderBuilder = $objectManager->create('\Magento\Framework\Api\SortOrderBuilder');
                $sortOrderBuilders = array(
                    $sortOrderBuilder->setField("updated_at")->setDirection("ASC")->create()
                );
                $searchCriteriaBuilder
                    ->setSortOrders($sortOrderBuilders)
                    ->setPageSize($maxperpage)
                    //->addFilter('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED, 'eq')
                    ->addFilter('updated_at', $start, 'gteq')
                    ->addFilter('updated_at', $end, 'lteq');
                $quotes = $quoteRepository->getQuotesList($searchCriteriaBuilder->create())->getItems();
                if (count($quotes) > 0) {
                    foreach ($quotes as $indx => $quote) {
                        if (is_numeric($indx)) {
                            $quoteId = $quote->getId();
                            if ($quote->getTriggerRecollect()) {
                                $connection = $this->resource->getConnection();
                                $tableName = $this->resource->getTableName('quote');
                                $sql = "UPDATE " . $tableName . " SET trigger_recollect = 0 WHERE entity_id = " . $quoteId;
                                $connection->query($sql);
                            }
                            $quotationFactory = $objectManager->create('\Cart2Quote\Quotation\Model\Quote');
                            $quotation = $quotationFactory->load($quoteId);

                            $quoteFactory = $objectManager->create('\Magento\Quote\Model\Quote');
                            $quote = $quoteFactory->load($quoteId);
                            $result = $this->helper->convertAttributeData($quote);

                            $result['status'] = $quotation->getStatus();

                            $groupId = (int) $quote->getCustomerGroupId();
                            if (isset($custGroups[$groupId])) {
                                $result['customer_group'] = $custGroups[$groupId];
                            } else {
                                $result['customer_group'] = 'Guest';
                            }

                            $result['website_id']       = (isset($stores[$result['store_id']]['website_id']))?  $stores[$result['store_id']]['website_id']: $websiteId;
                            $result['store_url']        = (isset($stores[$result['store_id']]['store_url']))?  $stores[$result['store_id']]['store_url']: $this->helper->getBaseUrl();
                            $result['media_url']        = (isset($stores[$result['store_id']]['media_url']))?  $stores[$result['store_id']]['media_url']:$this->helper->getMediaUrl();
                            $result['shipping_address'] = $this->helper->convertAttributeData($quote->getShippingAddress());
                            $result['billing_address'] = $this->helper->convertAttributeData($quote->getBillingAddress());

                            $result['items'] = array();

                            $quoteItems = $quoteRepository->getItems($quoteId);
                            foreach ($quoteItems as $item) {
                                if (! $item->isDeleted() && ! $item->getParentItemId()) {
                                    $this->helper->loadCatalogData($item, $storeId, $websiteId, $multistore, $maxAssociated);
                                    $result['items'][] = $this->helper->convertAttributeData($item);
                                }
                            }

                            if (! ($multistore) && $storeId) {
                                if ($result['store_id'] != $storeId) {
                                    $result['items'] = array();
                                }
                            }

                            // make sure there are items before adding to return
                            if (count($result['items'])) {
                                $returnData[$quoteId] = $result;
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            return $this->outputError(self::ERROR_CODE_UNKNOWN_EXCEPTION, 'Unknown exception on request', $e);
        }

        return $this->outputJson(array(
            'quotes' => $returnData,
            'stores' => $stores
        ));
    }
}
