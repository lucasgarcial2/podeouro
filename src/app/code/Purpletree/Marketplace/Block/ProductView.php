<?php
/**
 * Purpletree_Marketplace ProductView
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

class ProductView extends \Magento\Framework\View\Element\Template
{
     /**
      * Constructor
      *
      * @param \Magento\Framework\App\Action\Context
      * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
      * @param \Purpletree\Marketplace\Model\ResourceModel\Reviews
      * @param \Magento\Framework\Registry
      * @param \Purpletree\Marketplace\Helper\Data
      *
      */

      public $registry;
      public $storeDetails;
      public $reviewDetails;
      public $dataHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Purpletree\Marketplace\Model\ResourceModel\Reviews $reviewDetails,
        \Magento\Framework\Registry $registry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        array $data = []
    ) {
           
        $this->registry = $registry;
        $this->storeDetails=$storeDetails;
        $this->reviewDetails=$reviewDetails;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Get Current Product
     *
     * @return Product
     */
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * Get Store Details
     *
     * @return Store Details
     */
    public function getStoreDetails($sellerId)
    {
        return $this->storeDetails->getStoreDetails($sellerId);
    }
    
    /**
     * Get Reviews Average
     *
     * @return Reviews Average
     */
    public function getReviewsAvg($sellerId)
    {
        return $this->reviewDetails->getReviewsAvg($sellerId);
    }
    
    /**
     * Get Reviews Count
     *
     * @return Reviews Count
     */
    public function getReviewsCount($sellerId)
    {
        return $this->reviewDetails->getReviewsCount($sellerId);
    }
    
    /**
     * Get Reviews Visible
     *
     * @return Reviews Visible
     */
    public function getReviewsVisible()
    {
        return $this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
    }
    public function isEnabled()
    {
        return $this->dataHelper->getGeneralConfig('general/enabled');
    }
}
