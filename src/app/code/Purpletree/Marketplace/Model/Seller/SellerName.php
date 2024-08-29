<?php
/**
 * Purpletree_Marketplace Isseller
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

class SellerName implements \Magento\Framework\Data\OptionSourceInterface
{
    public $sellerinfo;
    
    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellerinfo
    ) {
        $this->sellerinfo            = $sellerinfo;
    }
    /**
     * Retrieve all attribute options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $sellers = $this->sellerinfo->getAllSellers();
        $options = [];
        foreach ($sellers as $seller) {
            $options[] = [
                    'value' => $seller['seller_id'],
                'label' => $seller['store_name']
            ];
        }
        return $options;
    }
}
