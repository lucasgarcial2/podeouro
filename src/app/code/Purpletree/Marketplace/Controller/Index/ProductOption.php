<?php
/**
 * Purpletree_Marketplace ProductOption
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
namespace Purpletree\Marketplace\Controller\Index;

use \Magento\Framework\App\Action\Action;
use \Magento\Customer\Model\Session as CustomerSession;

class ProductOption extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    public $resultPageFactory;
    public $customer;
    public $customerRepository;
    public $storeManager;
    public $storeDetails;
    public $coreRegistry;
    public $resultForwardFactory;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->resultPageFactory        =       $resultPageFactory;
        $this->customer                 =       $customer;
        $this->customerRepository   =       $customerRepository;
        $this->storeManager             =       $storeManager;
        $this->storeDetails                 =       $storeDetails;
        $this->coreRegistry             =       $coreRegistry;
        $this->resultForwardFactory     =       $resultForwardFactory;
        $this->dataHelper               =       $dataHelper;
        parent::__construct($context);
    }

    public function execute()
    {
         $customerId=$this->customer->getCustomer()->getId();
         $isseller =0;
         $sellerId=$this->storeDetails->isavialableSeller($customerId);
             $customer = $this->customerRepository->getById($customerId);
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            $isseller =  $customer->getCustomAttribute('is_seller')->getValue();
        }
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
                $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
                $this->customer->authenticate();
        }
        
        if ($sellerId=='' || !$moduleEnable || $isseller == 0) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->coreRegistry->register('seller_id', $sellerId);
        $this->_resultPage = $this->resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Product Type'));
        return $this->_resultPage;
    }
}
