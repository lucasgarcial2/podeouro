<?php

/**
 * Purpletree_Marketplace Region
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

namespace Purpletree\Marketplace\Model\Seller;

class Region
{
    public $_countryFactory;
    
    public function __construct(\Magento\Directory\Model\CountryFactory $countryFactory)
    {
         $this->_countryFactory = $countryFactory;
    }
    public function toOptionArray($countrycode)
    {
            return $this->_countryFactory->create()->setId($countrycode)->getLoadedRegionCollection()->toOptionArray();
    }
}
