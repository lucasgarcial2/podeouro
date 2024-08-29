<?php

/**
 * Purpletree_Marketplace Seller
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Model\ResourceModel;

class Seller extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public $_date;
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
    
        $this->_date = $date;
        parent::__construct($context);
    }
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_stores', 'entity_idpts');
    }

    /**
     * Retrieves Seller Name from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getSellerNameById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from($this->getMainTable(), 'store_name')
            ->where('entity_idpts = :entity_idpts');
        $binds = ['entity_idpts' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    public function getSellerIdById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from($this->getMainTable(), 'seller_id')
            ->where('entity_idpts = :entity_idpts');
        $binds = ['entity_idpts' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }
    
    /**
     * Geting Seller Name Seller Id
     *
     * @return Seller Name
     */
    public function getSellerNameBySellerId($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'store_name')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $id);
        return $adapter->fetchOne($select);
    }
    
    /**
     * Geting Seller Entity Id
     *
     * @return Seller Entity Id
     */
    public function getsellerEntityId($id)
    {
        $adapter = $this->getConnection($id);
        $sql = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'entity_idpts')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $id);
        return $adapter->fetchOne($sql);
    }
    /**
     * Checking Store url
     *
     * @return Store URL
     */
    public function checkStoreurl($id, $value)
    {
        $adapter = $this->getConnection($id, $value);
        $sql = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'entity_idpts')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.store_url = ?', $value);
        return $adapter->fetchAll($sql);
    }
    
    /**
     * Checking URL Uniqueness
     *
     * @return Store URL
     */
    public function checkUniqueUrl($store_url)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
				->from($this->getMainTable(), 'store_url')
            ->where('store_url = :store_url');
            $binds = ['store_url' => $store_url];
        return $adapter->fetchAll($select, $binds);
    }
    public function checkUniqueUrlcustomer($store_url)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from($this->getMainTable(), 'seller_id')
            ->where('store_url = :store_url');
            $binds = ['store_url' => $store_url];
        return $adapter->fetchOne($select, $binds);
    }
    /**
     * Get all sellers
     *
     * @return Store URL
     */
    public function getAllSellers()
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable());
        return $adapter->fetchAll($select);
    }
    
    /**
     * Geting Store ID
     *
     * @return Store ID
     */
    public function storeId($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from($this->getMainTable(), 'entity_idpts')
            ->where('seller_id = :seller_id');
            $binds = ['seller_id' => $sellerId];
        return $adapter->fetchOne($select, $binds);
    }
    
    /**
     * Geting Store Details
     *
     * @return Store Details
     */
    public function getStoreDetails($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('seller_id = :seller_id');
            $binds = ['seller_id' => $sellerId];
        return $adapter->fetchRow($select, $binds);
    }
    
    /**
     * Geting Store Id By Store Url
     *
     * @return Store Id
     */
    public function storeIdByUrl($storeUrl)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from($this->getMainTable(), 'seller_id')
            ->where('store_url = :store_url');
            $binds = ['store_url' => $storeUrl];
        return $adapter->fetchOne($select, $binds);
    }
    
    /**
     * Checking seller or not
     *
     * @return Seller Id
     */
    public function isSeller($custId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from(['cl' => $this->getMainTable()], 'seller_id')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $custId)
            ->where('cl.status_id = ?', 1);
        return $adapter->fetchOne($select);
    }
    public function isavialableSeller($custId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
				->from(['cl' => $this->getMainTable()], 'seller_id')
				->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
				->where('cl.seller_id = ?', $custId);
        return $adapter->fetchOne($select);
    }
    
    /**
     * Checking seller or not
     *
     * @return Seller Id
     */
    public function isSellerApprove($custId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'status_id')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $custId);
        return $adapter->fetchOne($select);
    }
    
    /**
     * Checking seller or not
     *
     * @return Seller Id
     */
    public function isSellerCheck($custId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'seller_id')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $custId);
        return $adapter->fetchOne($select);
    }
    
    /**
     * Geting Seller Id By Customer Id
     *
     * @return Seller Id
     */
    public function getSellerIdByCustomerId($custId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
			->from(['cl' => $this->getMainTable()], 'seller_id')
			->joinRight(['cv' => $this->_resources->getTableName('customer_entity')], 'cv.entity_id = cl.seller_id', ['entity_id'])
            ->where('cl.seller_id = ?', $custId);
        return $adapter->fetchOne($select);
    }
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Purpletree\Marketplace\Model\Seller $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->_date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->_date->date());
        }
        return parent::_beforeSave($object);
    }
    public function toArray()
    {
        return $this->getAllSellers();
    }

    public function toOptionArray()
    {
        $options = [];
        foreach ($this->getAllSellers() as $seller) {
            $options[] = [
                'value' => $seller['seller_id'],
                'label' => $seller['store_name']
            ];
        }

        return $options;
    }
}
