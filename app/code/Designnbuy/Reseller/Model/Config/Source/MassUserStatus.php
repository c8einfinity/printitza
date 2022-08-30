<?php
/**
 * Created by PhpStorm.
 * User: Ashok
 * Date: 08-Jun-17
 * Time: 2:25 PM
 */

namespace Designnbuy\Reseller\Model\Config\Source;

/**
 * Status
 * @category Magestore
 * @package  Magestore_Bannerslider
 * @module   Bannerslider
 * @author   Magestore Developer
 */
class MassUserStatus
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;

    /**
     * get available statuses.
     *
     * @return []
     */
    public static function getAvailableStatuses()
    {
        return [
            self::STATUS_ENABLED => __('Enabled')
            ,self::STATUS_DISABLED => __('Disabled'),
        ];
    }
}
