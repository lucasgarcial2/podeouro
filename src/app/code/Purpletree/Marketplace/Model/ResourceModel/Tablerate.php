<?php

/**
 * Purpletree_Marketplace Tablerate
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

class Tablerate extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
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
    public function getPayment($countryid, $zipcode, $sellerId)
    {
        $adapter = $this->getConnection();
        $sql = $adapter->select()
            ->from($this->getMainTable())
                        ->where('dest_country_id = ?', $countryid)
                        ->where('dest_zip = ?', $zipcode)
                        ->where('seller_id = ?', $sellerId);
        return $adapter->fetchAll($sql);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pts_shipping_tablerate', 'pk');
    }
}
