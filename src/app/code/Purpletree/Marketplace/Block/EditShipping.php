<?php
/**
 * Purpletree_Marketplace EditShipping
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

use Magento\Directory\Model\Region;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Directory\Model\Country;


class EditShipping extends \Magento\Framework\View\Element\Template
{
     /**
      * Constructor
      *
      * @param \Magento\Framework\View\Element\Template\Context
      * @param \Magento\Framework\Registry
      * @param array $data
      */

      public $countryFactory;
      public $directoryHelper;
      public $countryHelper;
      public $currency;
      public $datahelper;
      public $countryInformationAcquirer;
      public $region;
      public $coreRegistry;
      public $tablerateCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Model\TablerateFactory $tablerateCollectionFactory,
		\Purpletree\Marketplace\Helper\Data $datahelper,
		\Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer,
		\Magento\Directory\Model\CountryFactory $countryFactory,
		 \Magento\Directory\Model\Config\Source\Country $countryHelper,
		 Region $region,
		   \Magento\Directory\Model\Currency $currency,
		 DirectoryHelper $directoryHelper,
        array $data = []
    ) {
		$this->countryFactory      		    =       $countryFactory;
		$this->directoryHelper = $directoryHelper;
        $this->countryHelper        		=       $countryHelper;
		     $this->currency             			= $currency;
		$this->datahelper 					= $datahelper;
		$this->countryInformationAcquirer   = $countryInformationAcquirer;
		$this->region 						= $region;
        $this->coreRegistry        			= $coreRegistry;
        $this->tablerateCollectionFactory = $tablerateCollectionFactory;
        parent::__construct($context, $data);
    }
    
    /**
     * Attribute
     *
     * @return Attribute
     */
    public function getShipping()
    {
         return $this->coreRegistry->registry('current_shipping');
    }
	    /**
     * Currency Symbol
     *
     * @return Currency Symbol
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
	/**
	 * Seller ID
	 *
	 * @return Seller ID
	 */
    public function sellerid()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }
		public function getConditionName()
    {
		return $this->datahelper->getConfigValue('carriers/purpletreetablerate/condition_name',$this->_storeManager->getStore()->getId());
	}
	 public function getCountryName($countrycode)
    {
        $countryName = '';
        $country = $this->countryFactory->create()->loadByCode($countrycode);
        if ($country) {
            $countryName = $country->getName();
        }
        return $countryName;
    }
	public function getRegionName($regionId)
    {
		$regionName = '';
			try {
			$regio = $this->region->load($regionId);
			if($regio) {
				$regionName = $regio->getName();
			}
			} catch (Exception $e) {
				
			}
		return $regionName.$regionId;
    }
	public function getCountryCollection()
	{
		$countries = array();
		$countryy = array();
		foreach ($this->directoryHelper->getCountryCollection() as $country) {
			if($this->ifgetAllowedCountry() == 0 || in_array($country->getId(),$this->getAllowedCountry())) {
				if($country->getName() != '') {
					$countries[$country->getId()] = $country->getName();
				}
			}
        }
		asort($countries);
		return $countries;
	
	}
		public function ifgetAllowedCountry() {
			return $this->datahelper->getConfigValue('carriers/purpletreetablerate/sallowspecific',$this->_storeManager->getStore()->getId());
		}
		public function getAllowedCountry()
	{
		$allowarray = array();
	
		if($this->ifgetAllowedCountry() == 1) {
			$allowarray = explode(',',$this->datahelper->getConfigValue('carriers/purpletreetablerate/specificcountry',$this->_storeManager->getStore()->getId()));
		}
		return $allowarray;
	}
	  public function getRegionByCountry($id)
    {
        $stateArray = $this->countryFactory->create()->setId($id)->getLoadedRegionCollection()->toOptionArray();
        return $stateArray;
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
}
