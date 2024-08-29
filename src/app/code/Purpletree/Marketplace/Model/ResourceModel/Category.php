<?php

/**
 * Purpletree_Marketplace Category
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

namespace Purpletree\Marketplace\Model\ResourceModel;

class Category extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_categories', 'entity_id');
    }

    /**
     * Retrieves Seller Name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function deleteSellerCategories($id)
    {
        $adapter = $this->getConnection();
        //Delete Data from table
        $sql = "Delete FROM " . $this->getMainTable()." Where seller_id =".$id;
        $adapter->query($sql);
    }
    public function getSellerCatids($id)
    {
        $adapter = $this->getConnection($id);
        $sql = $adapter->select()
            ->from($this->getMainTable(), 'category_id')
                          ->where('seller_id = ?', $id);
        return $adapter->fetchAll($sql);
    }
}
