<?php

/**
 * Purpletree_Marketplace Uninstall
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

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_sellerorderinvoice'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_sellerorder'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_categorycommission'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_payments'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_commissions'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_reviews'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_stores'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_vendorcontact'));
        $installer->getConnection()->dropTable($installer->getTable('purpletree_marketplace_categories'));
        $installer->getConnection()->dropTable($installer->getTable('pts_shipping_tablerate'));
        $installer->endSetup();
    }
}
