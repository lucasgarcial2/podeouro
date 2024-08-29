<?php

/**
 * Purpletree_Marketplace InstallData
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

use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\AttributeManagement;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Init
     *
     * @param CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        CustomerSetupFactory $customerSetupFactory,
        EavSetupFactory $eavSetupFactory,
        AttributeFactory $attributeFactory,
        AttributeManagement $attributeManagement,
        TypeFactory $typeFactory,
        AttributeSetFactory $attributeSetFactory
    ) {
        $this->salesSetupFactory = $salesSetupFactory;
        $this->attributeManagement = $attributeManagement;
        $this->attributeFactory = $attributeFactory;
        $this->eavTypeFactory = $typeFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
         /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'seller_id',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Seller Id',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => false,
                'is_used_in_grid' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => ''
            ]
        );
         $eavSetup->addAttribute(
             \Magento\Catalog\Model\Product::ENTITY,
             'is_seller_product',
             [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Is Seller Product',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'searchable' => false,
                'filterable' => true,
                'is_used_in_grid' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'is_visible_in_grid' => true,
                'used_in_product_listing' => true,
                'is_filterable_in_grid' => 1,
                'unique' => false,
                'apply_to' => ''
             ]
         );
        $this->addAttributeToAllAttributeSets('seller_id');
        $this->addAttributeToAllAttributeSets('is_seller_product');
        /** @var CustomerSetup $customerSetup */
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $attributesInfo = [
            'is_seller' => [
                "sort_order" => 100,
                "position" => 100,
                "system" => 0,
                "is_used_in_grid" => true,
                "type"     => "int",
                "backend"  => "",
                "label"    => "Is Seller",
                "input"    => "boolean",
                "source"   => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                "visible"  => false,
                "required" => false,
                "default" => 0,
                "frontend" => "",
                "unique"     => false,
                "user_defined"  => true,
            ]
        ];
         $customerEntity = $customerSetup->getEavConfig()->getEntityType('customer');
        $attributeSetId = $customerEntity->getDefaultAttributeSetId();
        /** @var $attributeSet AttributeSet **/
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);
        foreach ($attributesInfo as $attributeCode => $attributeParams) {
            $customerSetup->addAttribute(Customer::ENTITY, $attributeCode, $attributeParams);
        }
        $magentoUsernameAttribute = $customerSetup->getEavConfig()->getAttribute(Customer::ENTITY, 'is_seller');
        $magentoUsernameAttribute->addData([
            'attribute_set_id' => $attributeSetId,
            'attribute_group_id' => $attributeGroupId,
        ]);
        $magentoUsernameAttribute->save();
        
             /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();

        //Add attributes to quote
        $entityAttributesCodes = [
            'franchise_id' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
        ];

        foreach ($entityAttributesCodes as $code => $type) {
            $salesInstaller->addAttribute('order', $code, ['type' => $type, 'length'=> 255, 'is_used_in_grid' => true, 'label' => 'Seller  ID', 'visible' => true,'nullable' => true,]);
        }

        $orderTable = 'sales_order';
        $orderGridTable = 'sales_order_grid';

        //Order table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'seller_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'comment' =>'Seller ID'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderTable),
                'is_seller',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'comment' =>'Is Seller'
                ]
            );

        //Order Grid table
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderGridTable),
                'seller_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'length' => 255,
                    'comment' =>'Seller ID`'
                ]
            );
        $setup->getConnection()
            ->addColumn(
                $setup->getTable($orderGridTable),
                'is_seller',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    'comment' =>'Is Seller'
                ]
            );
			 $datashippingrates = [
            [
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 0,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 0.0000,
                'price' => 15.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 0,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 10.0000,
                'price' => 10.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 0,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 100.0000,
                'price' => 5.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 2,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 0.0000,
                'price' => 20.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 2,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 50.0000,
                'price' => 15.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 2,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 100.0000,
                'price' => 10.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 21,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 0.0000,
                'price' => 20.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 21,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 15.0000,
                'price' => 50.0000 ,
                'cost' => 0.0000,
            ],[
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 21,
                'dest_zip' => "*",
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 100.0000,
                'price' => 10.0000 ,
                'cost' => 0.0000,
            ]
        ];
        $pts_shipping_tablerate = $setup->getTable('pts_shipping_tablerate');
		if($setup->getConnection()->isTableExists($pts_shipping_tablerate)) {
			foreach ($datashippingrates as $data) {
				$setup->getConnection()->insert($pts_shipping_tablerate, $data);
			}
		}
        $setup->endSetup();
    }
    public function addAttributeToAllAttributeSets($attributeCode)
    {
    /** @var Attribute $attribute */
        $entityType = $this->eavTypeFactory->create()->loadByCode('catalog_product');
        $attribute = $this->attributeFactory->create()->loadByCode($entityType->getId(), $attributeCode);
    
        if (!$attribute->getId()) {
            return false;
        }
    
    /** @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\Collection $setCollection */
        $setCollection = $this->attributeSetFactory->create()->getCollection();
        $setCollection->addFieldToFilter('entity_type_id', $entityType->getId());
    
    /** @var Set $attributeSet */
        foreach ($setCollection as $attributeSet) {
            $groupId = $attributeSet->getDefaultGroupId();
            $this->attributeManagement->assign(
                'catalog_product',
                $attributeSet->getId(),
                $groupId,
                $attributeCode,
                $attributeSet->getCollection()->getSize() * 10
            );
        }
    
        return true;
    }
}
