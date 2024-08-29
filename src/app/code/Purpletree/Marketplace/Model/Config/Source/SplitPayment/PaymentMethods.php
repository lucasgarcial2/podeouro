<?php
/**
 * Purpletree_Marketplace Category
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Model\Config\Source\SplitPayment;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class for Showing the list of payment
 * methods available for split payments
 */
class PaymentMethods implements ArrayInterface
{

    /**
     * {@inheritDoc}
     *
     */
    public function toOptionArray()
    {
        
        return [
            [
                "value" => "stripe",
                "label" => "Stripe"
            ],
            [
                "value" => "paypal",
                "label" => "Paypal"
            ],
        ];
    }
}