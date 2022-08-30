<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Designnbuy\Customer\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'customer_design'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('designnbuy_customer_design'))
            ->addColumn(
                'design_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Design Id'
            )
            ->addColumn(
                'design_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Design Name'
            )
            ->addColumn(
                'product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Product Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Store Id'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Id'
            )
            ->addColumn(
                'svg',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Design SVG'
            )
            ->addColumn(
                'png',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => true, 'default' => null],
                'Design PNG'
            )
            ->addColumn(
                'options',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                [],
                'Options'
            )
            ->addColumn(
                'design_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Design Status'
            )
            ->addColumn(
                'tool_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Tool Type'
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_design', ['customer_id']),
                ['customer_id']
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_design', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('designnbuy_customer_design', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_SET_NULL
            )
            ->setComment('Customer Design');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customer_template'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('customer_template'))
            ->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Template ID'
            )
            ->addColumn(
                'template_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                150,
                [],
                'Template Code'
            )
            ->addColumn(
                'template_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Template Text'
            )
            ->addColumn(
                'template_styles',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Template Styles'
            )
            ->addColumn(
                'template_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Template Type'
            )
            ->addColumn(
                'template_subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Template Subject'
            )
            ->addColumn(
                'template_sender_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Template Sender Name'
            )
            ->addColumn(
                'template_sender_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Template Sender Email'
            )
            ->addColumn(
                'template_actual',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'default' => '1'],
                'Template Actual'
            )
            ->addColumn(
                'added_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Added At'
            )
            ->addColumn(
                'modified_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Modified At'
            )
            ->addIndex(
                $installer->getIdxName('customer_template', ['template_actual']),
                ['template_actual']
            )
            ->addIndex(
                $installer->getIdxName('customer_template', ['added_at']),
                ['added_at']
            )
            ->addIndex(
                $installer->getIdxName('customer_template', ['modified_at']),
                ['modified_at']
            )
            ->setComment('Customer Template');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_customer_queue'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('designnbuy_customer_queue'))
            ->addColumn(
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Queue Id'
            )
            ->addColumn(
                'template_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Template ID'
            )
            ->addColumn(
                'customer_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [],
                'Customer Type'
            )
            ->addColumn(
                'customer_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Customer Text'
            )
            ->addColumn(
                'customer_styles',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Customer Styles'
            )
            ->addColumn(
                'customer_subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Customer Subject'
            )
            ->addColumn(
                'customer_sender_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Customer Sender Name'
            )
            ->addColumn(
                'customer_sender_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Customer Sender Email'
            )
            ->addColumn(
                'queue_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Queue Status'
            )
            ->addColumn(
                'queue_start_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Queue Start At'
            )
            ->addColumn(
                'queue_finish_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Queue Finish At'
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_queue', ['template_id']),
                ['template_id']
            )
            ->addForeignKey(
                $installer->getFkName('designnbuy_customer_queue', 'template_id', 'customer_template', 'template_id'),
                'template_id',
                $installer->getTable('customer_template'),
                'template_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Customer Queue');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_customer_queue_link'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('designnbuy_customer_queue_link'))
            ->addColumn(
                'queue_link_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Queue Link Id'
            )
            ->addColumn(
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Queue Id'
            )
            ->addColumn(
                'design_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Design Id'
            )
            ->addColumn(
                'letter_sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Letter Sent At'
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_queue_link', ['design_id']),
                ['design_id']
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_queue_link', ['queue_id', 'letter_sent_at']),
                ['queue_id', 'letter_sent_at']
            )
            ->addForeignKey(
                $installer->getFkName('designnbuy_customer_queue_link', 'queue_id', 'designnbuy_customer_queue', 'queue_id'),
                'queue_id',
                $installer->getTable('designnbuy_customer_queue'),
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName(
                    'designnbuy_customer_queue_link',
                    'design_id',
                    'designnbuy_customer_design',
                    'design_id'
                ),
                'design_id',
                $installer->getTable('designnbuy_customer_design'),
                'design_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Customer Queue Link');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'designnbuy_customer_queue_store_link'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('designnbuy_customer_queue_store_link'))
            ->addColumn(
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Queue Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true, 'default' => '0'],
                'Store Id'
            )
            ->addIndex(
                $installer->getIdxName('designnbuy_customer_queue_store_link', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('designnbuy_customer_queue_store_link', 'queue_id', 'designnbuy_customer_queue', 'queue_id'),
                'queue_id',
                $installer->getTable('designnbuy_customer_queue'),
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('designnbuy_customer_queue_store_link', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Customer Queue Store Link');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'customer_problem'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('customer_problem'))
            ->addColumn(
                'problem_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Problem Id'
            )
            ->addColumn(
                'design_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true],
                'Design Id'
            )
            ->addColumn(
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Queue Id'
            )
            ->addColumn(
                'problem_error_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'default' => '0'],
                'Problem Error Code'
            )
            ->addColumn(
                'problem_error_text',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                200,
                [],
                'Problem Error Text'
            )
            ->addIndex(
                $installer->getIdxName('customer_problem', ['design_id']),
                ['design_id']
            )
            ->addIndex(
                $installer->getIdxName('customer_problem', ['queue_id']),
                ['queue_id']
            )
            ->addForeignKey(
                $installer->getFkName('customer_problem', 'queue_id', 'designnbuy_customer_queue', 'queue_id'),
                'queue_id',
                $installer->getTable('designnbuy_customer_queue'),
                'queue_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('customer_problem', 'design_id', 'designnbuy_customer_design', 'design_id'),
                'design_id',
                $installer->getTable('designnbuy_customer_design'),
                'design_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('Customer Problems');
        $installer->getConnection()->createTable($table);


        /**
         * Create table 'designnbuy_customer_images'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('designnbuy_customer_images'))
            ->addColumn(
                'image_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Image ID'
            )
            ->addColumn(
                'customer_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Customer Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true, 'default' => null],
                'Name'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'Image'
            )->addColumn(
                'hd_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                ['nullable' => true, 'default' => null],
                'HD Image'
            );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

    }
}
