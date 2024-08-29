<?php

/**
 * Purpletree_Marketplace Reviews
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

class Reviews extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
        $this->_date        =       $date;
        parent::__construct($context);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_reviews', 'entity_id');
    }

    /**
     * Geting Customer review or not
     *
     * @return Review
     */
    public function isReviewed($userId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('customer_id = :customer_id')
            ->where('status = :status');
            $binds = ['customer_id' => $userId,'status' => 1];
        return $adapter->fetchAll($select, $binds);
    }
    
    /**
     * Geting Reviews
     *
     * @return Reviews
     */
    public function getReviews()
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable());
        return $adapter->fetchAll($select);
    }
    
    /**
     * Geting Reviews Average
     *
     * @return Reviews Average
     */
    public function getReviewsAvg($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['AVG(rating)'])
            ->where('seller_id = :seller_id')
            ->where('status = :status');
            $binds = ['seller_id' => $sellerId,'status' => 1];
        return $adapter->fetchOne($select, $binds);
    }
    
    /**
     * Geting Reviews Count
     *
     * @return Reviews Count
     */
    public function getReviewsCount($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'COUNT(*)')
            ->where('seller_id = :seller_id')
             ->where('status = :status');
            $binds = ['seller_id' => $sellerId,'status' => 1];
        $result = (int)$adapter->fetchOne($select, $binds);
        return $result;
    }
    
    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Purpletree\Marketplace\Model\Reviews $object
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
}
