<?php
/**
 * Purpletree_Marketplace StoreView
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

class StoreView extends \Magento\Framework\View\Element\Template
{
   /**
    * Constructor
    *
    * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
    * @param \Purpletree\Marketplace\Model\ResourceModel\Reviews
    * @param \Magento\Framework\View\Element\Template\Context
    * @param \Magento\Framework\Registry
    * @param \Magento\Directory\Model\CountryFactory
    * @param \Magento\Store\Model\StoreManagerInterface
    * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
    * @param \Magento\Framework\Pricing\Helper\Data
    * @param \Magento\Directory\Model\RegionFactory
    * @param \Purpletree\Marketplace\Helper\Data
    * @param array $data
    */
    public $storeDetails;
    public $reviewDetails;
    public $coreRegistry;
    public $countryFactory;
    public $regionFactory;
    public $dataHelper;
    public $priceHelper;
    public $productCollectionFactory;

    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Purpletree\Marketplace\Model\ResourceModel\Reviews $reviewDetails,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->storeDetails                 =       $storeDetails;
        $this->reviewDetails            =       $reviewDetails;
        $this->coreRegistry             =       $coreRegistry;
        $this->countryFactory           =       $countryFactory;
        $this->regionFactory            =       $regionFactory;
        $this->dataHelper               =       $dataHelper;
        $this->priceHelper              =       $priceHelper;
        $this->productCollectionFactory =       $productCollectionFactory;
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
    
    /**
     * Get Country By Code
     *
     * @return Country List
     */
    public function getCountryByCode($countryId)
    {
        $country = $this->countryFactory->create()->loadByCode($countryId);
        return $country->getName();
    }
    
    /**
     * Get State By Code
     *
     * @return State List
     */
    public function getStateByCode($stateId)
    {
        $state = $this->regionFactory->create()->load($stateId);
        return $state->getName();
    }
    
    /**
     * Get Product Collection
     *
     * @return Product Collection
     */
    public function getProductCollection()
    {
        if (!$this->products) {
            $this->products  = $this->productCollectionFactory->create();
            $this->products->addAttributeToSelect('*');
        }
         return $this->products;
    }
    
    /**
     * Get Product Image Url
     *
     * @return Product Image Url
     */
    public function getProductImageUrl()
    {
		$destinationFolder =  $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
        return $destinationFolder;
    }
    
    /**
     * Get Product Url
     *
     * @return Product Url
     */
    public function getProductUrl()
    {
        $destinationFolder = $this->_storeManager->getStore()->getBaseUrl();
        return $destinationFolder;
    }
    
    /**
     * Get Formeted Price
     *
     * @return Price
     */
    public function getFormetedPrice($price)
    {
        $formattedPrice = $this->priceHelper->currency($price, true, false);
        return $formattedPrice;
    }
    
    /**
     * Get Reviews Average
     *
     * @return Reviews Average
     */
    public function getReviewsAvg()
    {
        return $this->reviewDetails->getReviewsAvg($this->getSellerId());
    }
    
    /**
     * Get Reviews Count
     *
     * @return Reviews Count
     */
    public function getReviewsCount()
    {
        return $this->reviewDetails->getReviewsCount($this->getSellerId());
    }
    
    /**
     * Get Reviews Visibility
     *
     * @return Reviews Visibility
     */
    public function getReviewsVisible()
    {
        return $this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
    }
    public function getDataHelper()
    {
        return $this->dataHelper->getGeneralConfig('general/fieldstoshow');
    }
}
