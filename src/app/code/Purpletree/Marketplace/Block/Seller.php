<?php
/**
 * Purpletree_Marketplace Seller
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

use Purpletree\Marketplace\Helper\Data as MarketplaceHelper;

class Seller extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Directory\Model\Config\Source\Country
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Eav\Api\AttributeRepositoryInterface
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Registry
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param MarketplaceHelper $marketplaceHelper
     * @param array $data
     */

     public $countryFactory;
     public $countryHelper;
     public $coreRegistry;
     public $option;
     public $storeDetails;
     public $_productMetadataInterface;
     public $_marketplaceHelper;

    public function __construct(
        \Magento\Directory\Model\Config\Source\Country $countryHelper,
        \Magento\Catalog\Model\Product\AttributeSet\Options $option,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
        \Magento\Framework\View\Element\Template\Context $context,
        MarketplaceHelper $marketplaceHelper,
        array $data = []
    ) {
        $this->countryFactory        =       $countryFactory;
        $this->countryHelper        =       $countryHelper;
        $this->coreRegistry         =       $coreRegistry;
        $this->option               =       $option;
        $this->storeDetails             =       $storeDetails;
         $this->_productMetadataInterface             =       $productMetadataInterface;
         $this->_marketplaceHelper             =       $marketplaceHelper;
        parent::__construct($context, $data);
    }
    /**
     * Country List
     *
     * @return Country List
     */
    public function getCountry()
    {
        return $this->countryHelper->toOptionArray();
    }
    public function getVersion()
    {
        return $this->_productMetadataInterface->getVersion();
    }
    
    /**
     * Attribute set List
     *
     * @return Attribute set List
     */
    public function getOption()
    {
        return $this->option->toOptionArray();
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
    
    /**
     * Get Region By Country
     *
     * @return Region
     */
    public function getRegionByCountry($id)
    {
        $stateArray = $this->countryFactory->create()->setId($id)->getLoadedRegionCollection()->toOptionArray();
        return $stateArray;
    }
    
    /**
     * Get Image Url
     *
     * @return Image Url
     */
    public function getImageUrl()
    {
		$destinationFolder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $destinationFolder;
    }

    /**
     * Get Marketplace Helper
     *
     * @return MarketplaceHelper
     */
    public function getMarketplaceHelper()
    {
        return $this->_marketplaceHelper;
    }
}
