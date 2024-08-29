<?php
/**
 * Purpletree_Marketplace InstallSchema
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
          $installer->getConnection()->addColumn(
              $installer->getTable('purpletree_marketplace_stores'),
              'store_commission',
              [
                    'type' => Table::TYPE_FLOAT,
                    'precision' => 10,
                    'scale' => 4,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Store Commission'
                ]
          );
		  $installer->getConnection()->addColumn(
              $installer->getTable('purpletree_marketplace_sellerorder'),
              'shipping',
              [
                    'type' => Table::TYPE_FLOAT,
                    'precision' => 12,
                    'scale' => 4,
                    'nullable' => true,
                    'default' => null,
                    'comment' => 'Shipping'
                ]
          );
            // Create Category Commission Table
        $categoryCommissionTableName = $installer->getTable('purpletree_marketplace_categorycommission');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($categoryCommissionTableName) != true) {
            $categoryCommissionTable = $installer->getConnection()
                ->newTable($categoryCommissionTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Category ID'
                )
                ->addColumn(
                    'commission',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false],
                    'Commission'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->setComment('Purpletree Vendor Category Commission')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($categoryCommissionTable);
        }
        // Create Category Commission Table
        // Create Seller Order Table
        $sellerOrderTableName = $installer->getTable('purpletree_marketplace_sellerorder');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($sellerOrderTableName) != true) {
            $sellerOrderTable = $installer->getConnection()
                ->newTable($sellerOrderTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Order ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product ID'
                )
				 ->addColumn(
                    'shipping',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => true, 'default' => NULL],
                    'Shipping'
                )
                ->addColumn(
                    'order_status',
                    Table::TYPE_TEXT,
                    50,
                    ['unsigned' => true, 'nullable' => false],
                    'Order Status'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->setComment('Purpletree Vendor Seller Order')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($sellerOrderTable);
        }
        // Create Seller Order Table
        // Create Seller Order Invoice Table
        $sellerOrderInvoiceTableName = $installer->getTable('purpletree_marketplace_sellerorderinvoice');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($sellerOrderInvoiceTableName) != true) {
            $sellerOrderInvoiceTable = $installer->getConnection()
                ->newTable($sellerOrderInvoiceTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Order ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Seller ID'
                )
				->addColumn(
                    'comment',
                    Table::TYPE_TEXT,
                    250,
                    ['unsigned' => true, 'nullable' => false],
                    'Comment'
                )
                ->addColumn(
                    'updated_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Updated At'
                )
                ->addColumn(
                    'created_at',
                    Table::TYPE_DATETIME,
                    null,
                    ['nullable' => false],
                    'Created At'
                )
                ->setComment('Purpletree Vendor Seller Order Invoice')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($sellerOrderInvoiceTable);
        } else {
			  $installer->getConnection()->addColumn(
              $installer->getTable('purpletree_marketplace_sellerorderinvoice'),
              'comment',
              [
                    'type' => Table::TYPE_TEXT,
					'length' => 250,
                    'nullable' => false,
                    'default' => null,
                    'comment' => 'Comment'
                ]
          );
		}
		 $pts_shipping_tablerate = $installer->getTable('pts_shipping_tablerate');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($pts_shipping_tablerate) != true) {
            $pts_shipping_tablerateTable = $installer->getConnection()
                 ->newTable($pts_shipping_tablerate)
            ->addColumn(
            'pk',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Primary key'
        )->addColumn(
            'seller_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Seller Id'
        )->addColumn(
            'website_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Website Id'
        )->addColumn(
            'dest_country_id',
            Table::TYPE_TEXT,
            4,
            ['nullable' => false, 'default' => '0'],
            'Destination coutry ISO/2 or ISO/3 code'
        )->addColumn(
            'dest_region_id',
            Table::TYPE_INTEGER,
            null,
            ['nullable' => false, 'default' => '0'],
            'Destination Region Id'
        )->addColumn(
            'dest_zip',
            Table::TYPE_TEXT,
            10,
            ['nullable' => false, 'default' => '*'],
            'Destination Post Code (Zip)'
        )->addColumn(
            'condition_name',
            Table::TYPE_TEXT,
            30,
            ['nullable' => false],
            'Rate Condition name'
        )->addColumn(
            'condition_value',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Rate condition value'
        )->addColumn(
            'price',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Price'
        )->addColumn(
            'cost',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Cost'
        )->addIndex(
            $installer->getIdxName(
                'shipping_tablerate',
                ['website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value','seller_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['website_id', 'dest_country_id', 'dest_region_id', 'dest_zip', 'condition_name', 'condition_value','seller_id'],
            ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Shipping Tablerate'
        );
                
            $installer->getConnection()->createTable($pts_shipping_tablerateTable);
        } 
        // Create Seller Order Invoice Table
        $installer->endSetup();
    }
}
