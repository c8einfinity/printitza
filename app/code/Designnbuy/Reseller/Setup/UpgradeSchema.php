<?php
namespace Designnbuy\Reseller\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements  UpgradeSchemaInterface
{
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.0.1') < 0) {
            $tableName = $setup->getTable('designnbuy_resellers');
            if ($setup->getConnection()->isTableExists($tableName) == true) {                    
                $columns = [
                    'bank_detail' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'after' => 'company_registration_number',
                        'comment' => 'Bank Detail',
                    ],
                    'vat_number' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'after' => 'bank_detail',
                        'comment' => 'Vat Number',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '2.0.2') < 0) {
            $tableName = $setup->getTable('designnbuy_resellers');
            if ($setup->getConnection()->isTableExists($tableName) == true) {                    
                $columns = [
                    'productpool' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'after' => 'vat_number',
                        'comment' => 'Product Pool Number',
                    ],
                ];
                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        if (version_compare($context->getVersion(), '2.0.3') < 0) {
            $tableName = $setup->getTable('designnbuy_resellers');
            if ($setup->getConnection()->isTableExists($tableName) == true) {                    
                $columns = [
                    'product_commission' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'after' => 'productpool',
                        'comment' => 'Product Commission',
                    ],                   
                    'commission_type' => [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                        'nullable' => true,
                        'after' => 'product_commission',
                        'comment' => 'Commission Type',
                    ],
                ];

                $connection = $setup->getConnection();
                foreach ($columns as $name => $definition) {
                    $connection->addColumn($tableName, $name, $definition);
                }
            }
        }

        $setup->endSetup();
    }
}