<?php
/**
 * Purpletree_Marketplace ProductEdit
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

class ProductEdit extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Catalog\Api\ProductRepositoryInterface
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    public $_resultPageFactory;
    public $customer;
    public $productRepository;
    public $coreRegistry;
    public $storeManager;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
         $this->productRepository = $productRepository;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
        $this->resultForwardFactory =       $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        $productId  = $this->getRequest()->getParam('id');
		if(!$this->coreRegistry->registry('seller_products')) {
				$this->coreRegistry->register('seller_products', '1');
			} 
        $sellerProducts = $this->dataHelper->getSellerCollection($productId, $this->customer->getId());
        $sellerproductId = '';
        if ($sellerProducts->count()) {
            foreach ($sellerProducts as $sellerproduct) {
                $sellerproductId = $sellerproduct->getId();
            }
        }
        if ($seller=='' || !$moduleEnable || $sellerproductId == '') {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
			
        $product = $this->productRepository->getById($productId);
        $this->coreRegistry->register('current_product', $product);
        $this->coreRegistry->register('current_customer_id', $this->customer->getId());
        $this->_resultPage = $this->_resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Edit Product'));
		$this->coreRegistry->unregister('seller_products');
        return $this->_resultPage;
    }
}
