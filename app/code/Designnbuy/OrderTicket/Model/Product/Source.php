<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Model\Product;

/**
 * Source Model of Product's Attribute Enable ORDERTICKET
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Source extends \Magento\Eav\Model\Entity\Attribute\Source\Boolean
{
    /**
     * XML configuration path allow ORDERTICKET on product level
     */
    const XML_PATH_PRODUCTS_ALLOWED = 'sales/designnbuy_orderticket/enabled_on_product';

    /**
     * Constants - attribute value
     */
    const ATTRIBUTE_ENABLE_ORDERTICKET_YES = 1;

    const ATTRIBUTE_ENABLE_ORDERTICKET_NO = 0;

    const ATTRIBUTE_ENABLE_ORDERTICKET_USE_CONFIG = 2;

    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                ['label' => __('Yes'), 'value' => self::ATTRIBUTE_ENABLE_ORDERTICKET_YES],
                ['label' => __('No'), 'value' => self::ATTRIBUTE_ENABLE_ORDERTICKET_NO],
            ];
        }
        return $this->_options;
    }
}
