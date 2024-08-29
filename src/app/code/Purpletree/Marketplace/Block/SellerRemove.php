<?php
/**
 * Purpletree_Marketplace SellerRemove
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Block;

class SellerRemove extends \Magento\Framework\View\Element\Template
{
   /**
    * Constructor
    *
    * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
    * @param \Magento\Framework\Registry
    * @param \Magento\Framework\View\Element\Template\Context
    * @param array $data
    */
    public $coreRegistry;
    public $storeDetails;

    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry         =       $coreRegistry;
        $this->storeDetails             =       $storeDetails;
        parent::__construct($context, $data);
    }

    /**
     * Seller ID
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }
    
    /**
     * Store Details
     *
     * @return Store Details
     */
    public function getStoreDetails()
    {
        return $this->storeDetails->getStoreDetails($this->getSellerId());
    }
}
