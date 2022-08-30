<?php
/**
 * Catalog entity setup
 *
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Designnbuy\OrderTicket\Setup;

use Magento\Eav\Model\Entity\Setup\Context;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Group\CollectionFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class OrderTicketSetup extends EavSetup
{
    /**
     * Retrieve default ORDERTICKET item entities
     *
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getDefaultEntities()
    {
        $entities = [
            'orderticket' => [
                'entity_model' => 'Designnbuy\OrderTicket\Model\ResourceModel\OrderTicket',
                'attribute_model' => null,
                'table' => 'designnbuy_orderticket',
                'increment_model' => 'Magento\Eav\Model\Entity\Increment\NumericValue',
                'additional_attribute_table' => null,
                'entity_attribute_collection' => null,
                'increment_per_store' => 1,
                'attributes' => [],
            ],
        ];
        return $entities;
    }

}
