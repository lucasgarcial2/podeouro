<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Purpletree\Marketplace\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Catalog\Model\Product;
use Magento\Framework\DB\Ddl\Table;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\TypeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Eav\Model\AttributeManagement;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class AddColumnPatch implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param SalesSetupFactory $salesSetupFactory
     * @param CustomerSetupFactory $customerSetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeFactory $attributeFactory
     * @param AttributeManagement $attributeManagement
     * @param TypeFactory $typeFactory
     * @param AttributeSetFactory $attributeSetFactory
     */
    public $salesSetupFactory;
    public $attributeManagement;
    public $attributeFactory;
    public $eavTypeFactory;
    public $customerSetupFactory;
    public $attributeSetFactory;
    public $eavSetupFactory;

	public function __construct(
		ModuleDataSetupInterface $moduleDataSetup,
		SalesSetupFactory $salesSetupFactory,
		CustomerSetupFactory $customerSetupFactory,
		EavSetupFactory $eavSetupFactory,
		AttributeFactory $attributeFactory,
		AttributeManagement $attributeManagement,
		TypeFactory $typeFactory,
		AttributeSetFactory $attributeSetFactory
	)
    {
        $this->moduleDataSetup = $moduleDataSetup;
		$this->salesSetupFactory = $salesSetupFactory;
        $this->attributeManagement = $attributeManagement;
        $this->attributeFactory = $attributeFactory;
        $this->eavTypeFactory = $typeFactory;
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        try {
            $setup = $this->moduleDataSetup;
            $setup->startSetup();
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Product::ENTITY,
                'seller_id',
                [
                    'type' => 'int',
                    'backend' => '',
                    'frontend' => '',
                    'label' => 'Seller Id',
                    'input' => 'text',
                    'class' => '',
                    'source' => '',
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
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
                Product::ENTITY,
                'is_seller_product',
                [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Is Seller Product',
                'input' => 'boolean',
                'class' => '',
                'source' => '',
                'global' => '',
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
            $entityAttributesCodes = [
                'franchise_id' => Table::TYPE_TEXT,
            ];
    
            foreach ($entityAttributesCodes as $code => $type) {
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'length'=> 255, 'is_used_in_grid' => true, 'label' => 'Seller  ID', 'visible' => true,'nullable' => true,]);
            }
            $setup->endSetup();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @inheritdoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public static function getDependencies()
    {
        return [];
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