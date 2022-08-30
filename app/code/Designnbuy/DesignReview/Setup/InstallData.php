<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\DesignReview\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        //Fill table review/design_review_entity
        $reviewEntityCodes = [
            \Designnbuy\DesignReview\Model\Review::ENTITY_DESIGN_CODE,
            \Designnbuy\DesignReview\Model\Review::ENTITY_TEMPLATE_CODE
        ];
        foreach ($reviewEntityCodes as $entityCode) {
            $installer->getConnection()->insert($installer->getTable('design_review_entity'), ['entity_code' => $entityCode]);
        }

        //Fill table review/review_entity
        $reviewStatuses = [
            \Designnbuy\DesignReview\Model\Review::STATUS_APPROVED => 'Approved',
            \Designnbuy\DesignReview\Model\Review::STATUS_PENDING => 'Pending',
            \Designnbuy\DesignReview\Model\Review::STATUS_NOT_APPROVED => 'Not Approved',
        ];
        foreach ($reviewStatuses as $k => $v) {
            $bind = ['status_id' => $k, 'status_code' => $v];
            $installer->getConnection()->insertForce($installer->getTable('design_review_status'), $bind);
        }

        $data = [
            \Designnbuy\DesignReview\Model\Rating::ENTITY_PRODUCT_CODE => [
                ['rating_code' => 'Quality', 'position' => 0],
                ['rating_code' => 'Value', 'position' => 0],
                ['rating_code' => 'Price', 'position' => 0],
            ],
            \Designnbuy\DesignReview\Model\Rating::ENTITY_PRODUCT_REVIEW_CODE => [],
            \Designnbuy\DesignReview\Model\Rating::ENTITY_REVIEW_CODE => [],
        ];

        foreach ($data as $entityCode => $ratings) {
            //Fill table rating/rating_entity
            $installer->getConnection()->insert($installer->getTable('design_rating_entity'), ['entity_code' => $entityCode]);
            $entityId = $installer->getConnection()->lastInsertId($installer->getTable('design_rating_entity'));

            foreach ($ratings as $bind) {
                //Fill table rating/rating
                $bind['entity_id'] = $entityId;
                $installer->getConnection()->insert($installer->getTable('design_rating'), $bind);

                //Fill table rating/rating_option
                $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('design_rating'));
                $optionData = [];
                for ($i = 1; $i <= 5; $i++) {
                    $optionData[] = ['rating_id' => $ratingId, 'code' => (string)$i, 'value' => $i, 'position' => $i];
                }
                $installer->getConnection()->insertMultiple($installer->getTable('design_rating_option'), $optionData);
            }
        }
    }
}
