<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\Customer\Model\ResourceModel\Template;

/**
 * Customer templates collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model and model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Designnbuy\Customer\Model\Template', 'Designnbuy\Customer\Model\ResourceModel\Template');
    }

    /**
     * Load only actual template
     *
     * @return $this
     */
    public function useOnlyActual()
    {
        $this->addFieldToFilter('template_actual', 1);

        return $this;
    }

    /**
     * Returns options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('template_id', 'template_code');
    }
}
