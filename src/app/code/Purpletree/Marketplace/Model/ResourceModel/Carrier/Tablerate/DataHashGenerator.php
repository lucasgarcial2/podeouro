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
* @copyright   Copyright (c) 2020
* @license     https://www.purpletreesoftware.com/license.html
*/
namespace Purpletree\Marketplace\Model\ResourceModel\Carrier\Tablerate;

class DataHashGenerator
{
    /**
     * @param array $data
     * @return string
     */
    public function getHash(array $data)
    {
        $countryId = $data['dest_country_id'];
        $regionId = $data['dest_region_id'];
        $zipCode = $data['dest_zip'];
        $conditionValue = $data['condition_value'];
        $seller_id = $data['seller_id'];

        return sprintf("%s-%d-%s-%F-%d", $countryId, $regionId, $zipCode, $conditionValue,$seller_id);
    }
}
