<?php
/**
 * Purpletree_Marketplace SellerShipping
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

use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\Region;
use Magento\Directory\Helper\Data as DirectoryHelper;

class SellerShipping extends \Magento\Framework\View\Element\Template
{
    /**
     * shipping
     */
    protected $shipping;
    
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\Pricing\Helper\Data
     * @param \Purpletree\Marketplace\Model\TablerateFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Tablerate
     * @param array $data
     */

     public $directoryHelper;
     public $datahelper;
     public $countryInformationAcquirer;
     public $countryFactory;
     public $region;
     public $coreRegistry;
     public $_collectionFactory;
     public $saleDetails;
     public $shippingsDetails;
     public $priceHelper;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
         \Purpletree\Marketplace\Model\ResourceModel\Carrier\Tablerate\CollectionFactory $collectionFactory,
        \Purpletree\Marketplace\Helper\Data $datahelper,
        \Purpletree\Marketplace\Model\ResourceModel\Commission $saleDetails,
        \Purpletree\Marketplace\Model\ResourceModel\Tablerate $shippingsDetails,
		\Magento\Directory\Api\CountryInformationAcquirerInterface $countryInformationAcquirer,
		 CountryFactory $countryFactory,
		 DirectoryHelper $directoryHelper,
		 Region $region,
        array $data = []
    ) {
				$this->directoryHelper = $directoryHelper;
		$this->datahelper = $datahelper;
		    $this->countryInformationAcquirer = $countryInformationAcquirer;
		$this->countryFactory = $countryFactory;
		$this->region = $region;
        $this->coreRegistry                 =       $coreRegistry;
         $this->_collectionFactory = $collectionFactory;
        $this->saleDetails                  =       $saleDetails;
        $this->shippingsDetails              =       $shippingsDetails;
        $this->priceHelper                  =       $priceHelper;
        parent::__construct($context, $data);
    }
    
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getShippingRates()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.tablerate.pager'
            )->setCollection(
                $this->getShippingRates()
            );
            $this->setChild('pager', $pager);
            $this->getShippingRates();
        }
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    
    /**
     * shippings
     *
     * @return shippings
     */
    public function getShippingRates()
    {
		$website = $this->_storeManager->getWebsite();
        $conditionName = $website->getConfig('carriers/purpletreetablerate/condition_name');
        if (!$this->shipping) {
            	$collection = $this->_collectionFactory->create();
		$collection->setConditionFilter($conditionName)->setWebsiteFilter($website->getId())->addFieldToFilter('seller_id', $this->getSellerId());
            if ($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();
                $countryid     =   (isset($data['countryid']) && $data['countryid']!='')?$data['countryid']:'';
				if($countryid == '0') {
					$countryid = '';
				}
                $zipcode     =   (isset($data['zipcode']) && $data['zipcode']!='')?$data['zipcode']:'';
                $this->shipping = $this->getShippingRatesAjax($countryid, $zipcode, $collection);
            } else {
        $this->shipping = $collection;
            }
        }
        return $this->shipping;
    }
    public function getShippingRatesAjax($countryid, $zipcode, $collection)
    {
        if ($countryid =='' && $zipcode =='') {
		//echo "a"; die;
        } elseif ($countryid != '' && $zipcode == '') {
		//echo "b"; die;
             $collection->addFieldToFilter('dest_country_id',$countryid);
        } elseif ($zipcode != '' && $countryid == '') {
		//echo "c"; die;
            $collection->addFieldToFilter('dest_zip',$zipcode);
        } else {
		//echo "d"; die;
           $collection->addFieldToFilter('dest_zip',$zipcode)
						->addFieldToFilter('dest_country_id',$countryid);
        }
        return $collection;
    }
    /**
     * Formatted Price
     *
     * @return Formatted Price
     */
    public function getFormattedPrice($price)
    {
        $price=$this->priceHelper->currency($price, true, false);
        return $price;
    }
    
    /**
     *
     *
     * @return Seller ID
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }
	
	public function getConditionName()
    {
		return $this->datahelper->getConfigValue('carriers/purpletreetablerate/condition_name',$this->_storeManager->getStore()->getId());
	}
	 public function getCountryName($countrycode)
    {
		$countryname = trim($countrycode);
		if($countrycode == '' || $countrycode == '*' || $countrycode == '0') { 
			$countryname = 'All';
		} else {
			try {
		   $country = $this->countryFactory->create()->loadByCode($countrycode);
			$countryname = $country->getData()['iso3_code'];
			} catch (Exception $e) {
			
			}
		}
        return $countryname;
    }
	public function getRegionName($regionId)
    {
		$regionName = '';
			if($regionId == '' || $regionId == '*' || $regionId == '0' || $regionId == 0) { 
				$regionName = __('All');
			} else {
			try {
			$regio = $this->region->load($regionId);
			if($regio) {
				$regionName = $regio->getCode();
			}
			} catch (Exception $e) {
				
			}
			}
		return $regionName;
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
    
}
