<?php
/**
 * Purpletree_Marketplace VendorContact
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

class VendorContact extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\View\Element\Template\Context
     * @param array $data
     */
    public $coreRegistry;
    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry                 =       $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * @return Order ID
     *
     *
     */
    public function getSellerId()
    {
        $result = (int) $this->coreRegistry->registry('seller_id');
        return $result;
    }
    
    /**
     * @return Order ID
     *
     *
     */
    public function getReferralId()
    {
        $result =$this->coreRegistry->registry('referral');
        return $result;
    }
}
