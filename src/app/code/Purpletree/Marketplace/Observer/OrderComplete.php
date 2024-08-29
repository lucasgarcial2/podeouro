<?php

/**
 * Purpletree_Marketplace OrderComplete
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

namespace Purpletree\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;

class OrderComplete implements ObserverInterface
{
   /**
    * @param \Magento\Customer\Api\CustomerRepositoryInterface
    * @param \Magento\Customer\Model\CustomerFactory
    * @param \Purpletree\Marketplace\Helper\Data
    * @param \Magento\Sales\Api\Data\OrderInterface
    * @param \Magento\CatalogInventory\Api\StockStateInterface
    * @param \Magento\Catalog\Model\ProductRepository
    * @param \Magento\Framework\App\Config\ScopeConfigInterface
    * @param \Magento\Customer\Model\Customer
    * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
    * @param \Magento\Framework\Mail\Template\TransportBuilder
    * @param \Magento\Framework\Translate\Inline\StateInterface
    * @param \Magento\Customer\Model\ResourceModel\CustomerFactory
    */
    public $stockStateInterface;
    public $dataHelper;
    public $messageManager;
    public $order;
    public $productRepository;
    public $scopeConfig;
    public $customerData;
    public $storeDetails;

    public function __construct(
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Customer $customerData,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->stockStateInterface               =       $stockStateInterface;
        $this->dataHelper               =       $dataHelper;
        $this->messageManager = $messageManager;
        $this->order                    =       $order;
        $this->productRepository        =       $productRepository;
        $this->scopeConfig              =       $scopeConfig;
        $this->customerData                 =       $customerData;
        $this->storeDetails                 =       $storeDetails;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        
        $moduleEnable           = $this->dataHelper->getGeneralConfig('general/enabled');
        $enableLowNotification  = $this->dataHelper
        ->getGeneralConfig('inventry/enable_low_notification');
        $lowStockQty            = $this->dataHelper->getGeneralConfig('inventry/low_stock_qty');
        if ($moduleEnable && $enableLowNotification) {
            $orderids           = $observer->getEvent()->getOrderIds();
            foreach ($orderids as $orderid) {
                $order  = $this->order->load($orderid);
                foreach ($order->getItemsCollection() as $items) {
                    $product        = $this->productRepository->getById($items['product_id']);
                    $StockState     = $this->stockStateInterface;
                    $productQty         = $StockState
                    ->getStockQty($product->getId(), $product->getStore()->getWebsiteId());
                    $productUrl         = $product->getProductUrl();
                    $sellerId       = $product->getSellerId();
                    if (($sellerId !='') && ($lowStockQty >= $productQty)) {
                        $this->lowStockNotification($lowStockQty, $sellerId, $items['name'], $productUrl);
                    }
                }
            }
        }
    }
    
    /**
     *   Low Stock Notification Email
     *
     *
     */
    private function lowStockNotification($lowStockQty, $sellerId, $productName, $productUrl)
    {
        $status='';
        $storeData=$this->getStoreDetails($sellerId);
        $sellerName=$this->getSellerName($sellerId);
        $identifier    = 'low_notification_email';
        try {
            $emailTemplateVariables = [];
            $emailTemplateVariables['product_name'] = $productName;
            $emailTemplateVariables['low_stock_qty'] = $lowStockQty;
            $emailTemplateVariables['seller_name'] = $sellerName;
            $emailTemplateVariables['product_url'] = $productUrl;
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' => $sellerName,
             'email' =>$this->getSellerEmail($sellerId)
            ];
            $this->dataHelper->yourCustomMailSendMethod(
                $emailTemplateVariables,
                $sender,
                $receiver,
                $identifier
            );
        } catch (\Exception $e) {
            $this->messageManager
            ->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    
    /**
     * Admin Store Email
     *
     * @return  Admin Store Email
     */
    private function getStoreEmail()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_sales/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    private function getStoreName()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_sales/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Store Details
     *
     * @return Store Details
     */
    private function getStoreDetails($sellerId)
    {
        return $this->storeDetails->getStoreDetails($sellerId);
    }
    
    /**
     * Seller Name
     *
     * @return Seller Name
     */
    private function getSellerName($sellerId)
    {
        $cust=$this->customerData->load($sellerId);
        return $cust->getName();
    }
    private function getSellerEmail($sellerId)
    {
        $cust=$this->customerData->load($sellerId);
        return $cust->getEmail();
    }
}
