<?php
/**
 * Purpletree_Marketplace StoreViewDetailbanner
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

class StoreViewDetailbanner extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Registry
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param array $data
     */
    public $storeDetails;
    public $coreRegistry;
    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->storeDetails                 =       $storeDetails;
        $this->coreRegistry             =       $coreRegistry;
        parent::__construct($context, $data);
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
    
    /**
     * Get Image Url
     *
     * @return Image Url
     */
    public function getImageUrl()
    {
		return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    
    /**
     * Get Store Url
     *
     * @return Store Url
     */
    public function getStoreUrl()
    {
        return $this->coreRegistry->registry('store_url');
    }
    
    /**
     * Seller ID
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->storeDetails->storeIdByUrl($this->getStoreUrl());
    }
}
