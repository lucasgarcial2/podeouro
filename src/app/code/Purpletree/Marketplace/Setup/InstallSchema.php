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

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $productMetadataInterface = $objectManager
        ->create('Magento\Framework\App\ProductMetadataInterface')->getVersion();
        if (version_compare($productMetadataInterface, "2.3.0") == -1) {
            $commisss = ['nullable' => true];
        } else {
            $commisss = [
                            Table::OPTION_NULLABLE => true,
                            Table::OPTION_DEFAULT => null,
                            Table::OPTION_PRECISION => 10,
                            Table::OPTION_SCALE => 4,
                        ];
        }
        $installer = $setup;
        $installer->startSetup();
 
        //Category table
        // Get purpletree_marketplace_stores table
        $ticketTableName = $installer->getTable('purpletree_marketplace_categories');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($ticketTableName) != true) {
            // Create purpletree_marketplace_stores table
            $ticketTable = $installer->getConnection()
                ->newTable($ticketTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'category_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Category ID'
                )
                 ->addColumn(
                     'created_at',
                     Table::TYPE_DATETIME,
                     null,
                     ['nullable' => false],
                     'Created At'
                 )
                    ->setComment('Purpletree Vendor Categories')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($ticketTable);
        }
        
        $vendorContact = $installer->getTable('purpletree_marketplace_vendorcontact');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($vendorContact) != true) {
            // Create purpletree_marketplace_stores table
            $ticketTable = $installer->getConnection()
                ->newTable($vendorContact)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Customer ID'
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Customer Email'
                )
                ->addColumn(
                    'customer_name',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Customer Name'
                )
                ->addColumn(
                    'customer_email',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Customer Email'
                )
                ->addColumn(
                    'customer_enquire',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Customer Enquire'
                )
                ->addColumn(
                    'customer_referral_url',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Customer Referral URL'
                )
                 ->addColumn(
                     'created_at',
                     Table::TYPE_DATETIME,
                     null,
                     ['nullable' => false],
                     'Created At'
                 )
                ->setComment('Purpletree Vendor Contact')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($ticketTable);
        }
        
        //Category table
        // Get purpletree_marketplace_stores table
        $ticketTableName = $installer->getTable('purpletree_marketplace_stores');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($ticketTableName) != true) {
            // Create purpletree_marketplace_stores table
            $ticketTable = $installer->getConnection()
                ->newTable($ticketTableName)
                ->addColumn(
                    'entity_idpts',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true,'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'store_name',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Store Name'
                )
                 ->addColumn(
                     'store_url',
                     Table::TYPE_TEXT,
                     30,
                     ['nullable' => false],
                     'Store Url'
                 )
                ->addColumn(
                    'store_logo',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Logo'
                )
                ->addColumn(
                    'store_phone',
                    Table::TYPE_TEXT,
                    30,
                    [],
                    'Phone'
                )
                ->addColumn(
                    'store_email',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Email'
                )
                ->addColumn(
                    'store_banner',
                    Table::TYPE_TEXT,
                    255,
                    [],
                    'Banner'
                )
                ->addColumn(
                    'store_commission',
                    Table::TYPE_FLOAT,
                    null,
                    $commisss,
                    'Store Commission'
                )
                ->addColumn(
                    'store_description',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Description'
                )
                ->addColumn(
                    'store_shipping_policy',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Shipping Policy'
                )
                ->addColumn(
                    'store_return_policy',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Return Policy'
                )
                ->addColumn(
                    'store_meta_keywords',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Meta Keywords'
                )
                ->addColumn(
                    'store_meta_descriptions',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Meta Description'
                )
                ->addColumn(
                    'store_address',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'Address'
                )
                ->addColumn(
                    'store_city',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'City'
                )
                ->addColumn(
                    'store_region',
                    Table::TYPE_TEXT,
                    100,
                    [],
                    'State'
                )
                ->addColumn(
                    'store_region_id',
                    Table::TYPE_INTEGER,
                    100,
                    [],
                    'Region ID'
                )
                ->addColumn(
                    'store_country',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => true],
                    'Country'
                )
                ->addColumn(
                    'store_zipcode',
                    Table::TYPE_TEXT,
                    12,
                    [],
                    'Zipcode'
                )
                ->addColumn(
                    'store_tin_number',
                    Table::TYPE_TEXT,
                    30,
                    [],
                    'TIN Number'
                )
                ->addColumn(
                    'store_bank_account',
                    Table::TYPE_TEXT,
                    '2M',
                    [],
                    'Bank Account'
                )
                ->addColumn(
                    'status_id',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['unsigned' => true,'nullable' => false],
                    'Status ID'
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
                ->setComment('Purpletree Vendor Stores')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');

            $installer->getConnection()->createTable($ticketTable);
            
            $installer->getConnection()->addIndex(
                $installer->getTable('purpletree_marketplace_stores'),
                $setup->getIdxName(
                    $installer->getTable('purpletree_marketplace_stores'),
                    ['store_name'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
                ),
                ['store_name'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_FULLTEXT
            );
        }
    
        // Get purpletree_marketplace_reviews table
        $ticketMessageTableName = $installer->getTable('purpletree_marketplace_reviews');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($ticketMessageTableName) != true) {
            // Create purpletree_marketplace_reviews table
            $ticketMessageTable = $installer->getConnection()
                ->newTable($ticketMessageTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'customer_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Customer ID'
                )
                ->addColumn(
                    'review_title',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => false],
                    'Title'
                )
                ->addColumn(
                    'review_description',
                    Table::TYPE_TEXT,
                    null,
                    [],
                    'Description'
                )
                ->addColumn(
                    'rating',
                    Table::TYPE_SMALLINT,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Rating'
                )->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Status'
                )->addColumn(
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
                ->addIndex(
                    $installer->getIdxName('purpletree_marketplace_reviews', ['seller_id']),
                    ['seller_id']
                )
                ->setComment('Purpletree Vendor Orders')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($ticketMessageTable);
        }
        
        // Get purpletree_marketplace_commissions table
        $ticketMessageTableName = $installer->getTable('purpletree_marketplace_commissions');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($ticketMessageTableName) != true) {
            // Create purpletree_marketplace_commissions table
            $ticketMessageTable = $installer->getConnection()
                ->newTable($ticketMessageTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'order_id',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Order ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product ID'
                )
                ->addColumn(
                    'product_name',
                    Table::TYPE_TEXT,
                    100,
                    ['nullable' => false],
                    'Product Name'
                )
                ->addColumn(
                    'product_price',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Product Price'
                )
                ->addColumn(
                    'product_quantity',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Product Quantity'
                )
                ->addColumn(
                    'commission',
                    Table::TYPE_DECIMAL,
                    '12,4',
                    ['nullable' => false],
                    'Commission'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Status'
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
                ->addIndex(
                    $installer->getIdxName('purpletree_marketplace_commissions', ['seller_id']),
                    ['seller_id']
                )
                ->setComment('Purpletree Vendor Orders')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($ticketMessageTable);
        }
        
        // Get purpletree_marketplace_payments table
        $ticketMessageTableName = $installer->getTable('purpletree_marketplace_payments');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($ticketMessageTableName) != true) {
            // Create purpletree_marketplace_payments table
            $ticketMessageTable = $installer->getConnection()
                ->newTable($ticketMessageTableName)
                ->addColumn(
                    'entity_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true,'unsigned' => true, 'nullable' => false,'primary' => true],
                    'Entity ID'
                )
                ->addColumn(
                    'seller_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Seller ID'
                )
                ->addColumn(
                    'transaction_id',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Transaction ID'
                )
                ->addColumn(
                    'amount',
                    Table::TYPE_DECIMAL,
                    null,
                    ['nullable' => false],
                    'Amount'
                )
                ->addColumn(
                    'payment_mode',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Payment Mode'
                )
                ->addColumn(
                    'status',
                    Table::TYPE_TEXT,
                    40,
                    ['nullable' => false],
                    'Status'
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
                ->addIndex(
                    $installer->getIdxName('purpletree_marketplace_payments', ['seller_id']),
                    ['seller_id']
                )
                ->setComment('Purpletree Vendor Orders')
                ->setOption('type', 'InnoDB')
                ->setOption('charset', 'utf8');
                
            $installer->getConnection()->createTable($ticketMessageTable);
        }
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
        }
        // Create Seller Order Invoice Table
		 // Create Seller Order Invoice Table
        $pts_shipping_tablerate = $installer->getTable('pts_shipping_tablerate');
        // Check if the table already exists
        if ($installer->getConnection()->isTableExists($pts_shipping_tablerate) != true) {
            $pts_shipping_tablerateeTable = $installer->getConnection()
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
                
            $installer->getConnection()->createTable($pts_shipping_tablerateeTable);
        }
 
        $installer->endSetup();
    }
}
