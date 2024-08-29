<?php

/**
 * Purpletree_Marketplace Payments
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

class Payments extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
     * Get Payment
     *
     * @return Payment
     */
    public function getPayment($fromDate, $toDate, $sellerId)
    {
        $adapter = $this->getConnection();
        $sql = $adapter->select()
            ->from($this->getMainTable())
                        ->where('date(created_at) >= ?', $fromDate)
                        ->where('date(created_at)<= ?', $toDate)
                        ->where('seller_id = ?', $sellerId);
        return $adapter->fetchAll($sql);
    }
    
    /**
     * Payments Details
     *
     * @return Payments Details
     */
    public function getPaytmentDetails($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), ['paid_amount'=>'SUM(amount)'])
            ->where('seller_id = :seller_id');
        $binds = ['seller_id' => $sellerId];
        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_payments', 'entity_id');
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
}
