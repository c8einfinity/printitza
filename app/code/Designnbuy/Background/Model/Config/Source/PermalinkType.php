<?php
/**
 * Copyright © 2017 Ajay Makwana (ajay.makwana@rightwaysolution.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Life is a code
 */

namespace Designnbuy\Background\Model\Config\Source;

use Designnbuy\Background\Model\Url;

/**
 * Used in creating options for permalink config value selection
 */
class PermalinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Url::PERMALINK_TYPE_DEFAULT, 'label' => __('Default: mystore.com/{background_route}/{background_route}/background-title/')],
            ['value' => Url::PERMALINK_TYPE_SHORT, 'label' => __('Short: mystore.com/{background_route}/background-title/')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }
}
