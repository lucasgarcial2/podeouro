<?php
/**
 * Purpletree_Marketplace BecomeSeller
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

class BecomeSeller extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Registry
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\View\Element\Template\Context
     * @param array $data
     */

     public $coreRegistry;
     public $storeDetails;
     public $customerRepository;

    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->coreRegistry         =       $coreRegistry;
        $this->storeDetails             =       $storeDetails;
        $this->customerRepository   =       $customerRepository;
        parent::__construct($context, $data);
    }

    /**
     * Get Seller Id
     *
     * @return Seller Id
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('seller_id');
    }
    
    /**
     * Get Store Details
     *
     * @return Store Details
     */
    public function getStoreDetails()
    {
        return $this->storeDetails->getStoreDetails($this->getSellerId());
    }
    
    /**
     * Get Customer Seller or not
     *
     * @return Seller
     */
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getSellerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    }
}
