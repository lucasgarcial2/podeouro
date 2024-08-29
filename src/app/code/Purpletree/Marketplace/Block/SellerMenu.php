<?php
/**
 * Purpletree_Marketplace SellerMenu
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

class SellerMenu extends \Magento\Framework\View\Element\Html\Link
{
    /**
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Framework\ObjectManagerInterface
     * @param \Magento\Framework\Stdlib\DateTime\DateTime
     * @param \Magento\Customer\Model\Session
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param array $data
     */

     public $customerSession;
     public $moduleManager;
     public $customerRepository;
     public $dataHelper;
     public $storeDetails;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
		\Magento\Framework\Module\Manager $moduleManager,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        array $data = []
    ) {
        $this->customerSession  =       $customerSession;
		$this->moduleManager 					= $moduleManager;
        $this->customerRepository   =       $customerRepository;
        $this->dataHelper       =       $dataHelper;
        $this->storeDetails         =       $storeDetails;
        parent::__construct($context, $data);
    }
   
    /**
     * Get Current Url
     *
     * @return Current Url
     */
    public function getAmastyShopbyBrand()
    {
       if ($this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand')) {
		return true;
	   }
	}
	   public function getPurpletreeShipping()
    {
       if ($this->dataHelper->getConfigValue('carriers/purpletreetablerate/active',$this->_storeManager->getStore()->getId())) {
		return true;
	  }
	return false;
    }
    public function getCurrentUrl()
    {
        return $this->getRequest()->getActionName();
    }
    
    /**
     * Get Customer Id
     *
     * @return Customer Id
     */
    public function getCustomerId()
    {
        $customerId = $this->customerSession->getCustomerId();
        return $customerId;
    }
    
    /**
     * Get Module Enabled
     *
     * @return Module Enabled
     */
    public function isModuleEnabled()
    {
        $moduleEnabled = $this->dataHelper->getGeneralConfig('general/enabled');
        return $moduleEnabled;
    }
    
    /**
     * Get Seller
     *
     * @return Seller
     */
    public function isSeller()
    {
        $seller=$this->storeDetails->isSeller($this->getCustomerId());
        return $seller;
    }

    public function storeDetails()
    {
        $seller=$this->storeDetails->getStoreDetails($this->getCustomerId());
        return $seller;
    }

    /**
     * Get Seller
     *
     * @return Seller Approve
     */
    public function isSellerApprove()
    {
        $seller=$this->storeDetails->isSellerApprove($this->getCustomerId());
        return $seller;
    }
    
    /**
     * Review Visibile or not
     *
     * @return Review
     */
    public function getReviewsVisible()
    {
        return $this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
    }
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getCustomerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    } 
}
