<?php

/**
 * Purpletree_Marketplace Collection
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

namespace Purpletree\Marketplace\Model\ResourceModel\Seller;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'entity_idpts';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'purpletree_marketplace_stores_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'seller_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Marketplace\Model\Seller', 'Purpletree\Marketplace\Model\ResourceModel\Seller');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Magento\Framework\DB\Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'entity_idpts', $labelField = 'store_name', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}