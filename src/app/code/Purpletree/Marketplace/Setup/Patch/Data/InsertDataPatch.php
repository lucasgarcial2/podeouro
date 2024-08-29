<?php
namespace Purpletree\Marketplace\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

class InsertDataPatch implements DataPatchInterface, PatchRevertableInterface
{
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('pts_shipping_tablerate');

        // Verificar se o registro já existe
        $select = $connection->select()
            ->from($tableName)
            ->where('website_id = ?', 1)
            ->where('dest_country_id = ?', 'US')
            ->where('dest_region_id = ?', 0)
            ->where('dest_zip = ?', '*')
            ->where('condition_name = ?', 'package_value_with_discount')
            ->where('condition_value = ?', 0);

        $result = $connection->fetchRow($select);

        // Se o registro não existir, insira
        if (!$result) {
            $connection->insert($tableName, [
                'website_id' => 1,
                'dest_country_id' => 'US',
                'dest_region_id' => 0,
                'dest_zip' => '*',
                'condition_name' => 'package_value_with_discount',
                'condition_value' => 0,
                'price' => 0,
                'cost' => 0,
            ]);
        }

        $this->moduleDataSetup->endSetup();
    }

    public function revert()
    {
        // Implementar a lógica de reversão se necessário
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }

    public static function getVersion()
    {
        return '1.0.0';
    }
}
