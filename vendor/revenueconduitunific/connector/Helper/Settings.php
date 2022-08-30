<?php

namespace Unific\Connector\Helper;

use Magento\Framework\Exception\InputException;
use Symfony\Component\Config\Definition\Exception\Exception;

class Settings extends \Magento\Framework\App\Helper\AbstractHelper
{
    const QUEUE_MAX_RETRIES = 3;
    const QUEUE_HISTORICAL_MAX_RETRIES = 5;
    const QUEUE_LIVE_PER_MINUTE = 50;
    const QUEUE_HISTORICAL_PER_MINUTE = 25;

    // Amount of days after the entries get automatically cleaned from the log
    const LOG_DAYS_RETENTION = 7;

    // The page size of historical data
    const LIVE_PAGE_SIZE = 10;
    const HISTORICAL_PAGE_SIZE = 50;

    // Priorities for placement in the queue
    const PRIORITY_CART = 1;
    const PRIORITY_CUSTOMER = 1;
    const PRIORITY_ORDER = 2;
    const PRIORITY_CATEGORY = 3;
    const PRIORITY_PRODUCT = 3;

    // Api integration statics
    const API_INTEGRATION_NAME = 'Unific-Integration';
    const API_INTEGRATION_EMAIL = 'info@unific.com';
    const API_INTEGRATION_ENDPOINT = 'https://api2.unific.com/';

    const QUEUE_ITEM_STATUS_PENDING = 0;
    const QUEUE_ITEM_STATUS_PROCESSING = 1;
    const QUEUE_ITEM_STATUS_SUCCESS = 2;
    const QUEUE_ITEM_STATUS_FAILURE = 3;
}
