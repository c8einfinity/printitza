<?php
/**
 * Customer store options
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Block\Design\Grid\Options;

class StoreOptionHash implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @param \Magento\Store\Model\System\Store $systemStore
     */
    public function __construct(\Magento\Store\Model\System\Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return store array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getStoreOptionHash();
    }
}
