<?php
/**
 * Purpletree_Marketplace View
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
 
namespace Purpletree\Marketplace\Block\Adminhtml\Sellerorder;

class View extends \Magento\Backend\Block\Template
{
    
    public $priceHelper;
    public $order;
    public $_customer;
    public $countryFactory;
    public $currency;
    public $_sellerOrder;
    public $_sellerOrderInvoice;
    public $statusCollectionFactory;
    public $_sellerorderFactory;
    public $seller;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Directory\Model\Currency $currency,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerOrder,
        \Purpletree\Marketplace\Model\SellerorderFactory $sellerorderFactory,
        \Magento\Customer\Model\Customer $customer,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $seller,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorderinvoice $sellerOrderInvoice,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        $this->priceHelper                      = $priceHelper;
        $this->order                            = $order;
        $this->_customer                        = $customer;
        $this->countryFactory                   = $countryFactory;
        $this->currency                         = $currency;
        $this->_sellerOrder                     = $sellerOrder;
        $this->_sellerOrderInvoice              = $sellerOrderInvoice;
        $this->statusCollectionFactory          = $statusCollectionFactory;
        $this->_sellerorderFactory              = $sellerorderFactory;
         $this->seller = $seller;
        parent::__construct($context);
    }
    public function getStatusOptions()
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();
        return $options;
    }
    public function saveSellerStatusUrl()
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();
        return $options;
    }
    public function getStatusOptio($code)
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();
        foreach ($options as $status) {
            if ($code == $status['value']) {
                return $status['label'];
            }
        }
    }
    public function getsellerstorename()
    {
        return $this->seller->getSellerNameBySellerId($this->getSellerId());
    }
    public function getseller()
    {
        return $this->_customer->load($this->getSellerId());
    }
    public function getEntityId()
    {
        return $this->getRequest()->getParam('entity_id');
    }
    public function getOrderId()
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
        $sellerorder = $this->_sellerorderFactory->create();
        $sellerorder->load($entity_id);
        return $sellerorder->getOrderId();
    }
    
    /**
     * @return Order Collection
     *
     *
     */
    public function getOrderCollection()
    {
        $orderId    = $this->getOrderId();
        return $this->order->load($orderId)->getData();
    }
    public function getSellerOrderStatus()
    {
        $orderId    = $this->getOrderId();
        $sellerId   = $this->getSellerId();
        $ddd        = $this->_sellerOrder->getSellerStatus($sellerId, $orderId);
        return $ddd['order_status'];
    }
    public function loadProductCollection()
    {
        $orderId            = $this->getOrderId();
        return $this->order->load($orderId)->getItemsCollection();
    }
    public function getCustomerIsGuest()
    {
         $orderId   = $this->getOrderId();
         $order = $this->order->load($orderId);
        return $order->getCustomerIsGuest();
    }
    public function getCustomer()
    {
         $orderId   = $this->getOrderId();
         $guest     = $this->getCustomerIsGuest();
        if ($guest) {
            return $this->getBillingAddress()['firstname'].$this->getBillingAddress()['lastname'];
        } else {
            $order = $this->order->load($orderId);
            return $order->getCustomerName();
        }
    }
    public function getCustomerId()
    {
         $orderId   = $this->getOrderId();
         $order = $this->order->load($orderId);
         $guest     = $this->getCustomerIsGuest();
        if (!$guest) {
            return $order->getCustomerId();
        }
    }
    public function getCustomerEmail()
    {
         $orderId   = $this->getOrderId();
         $order = $this->order->load($orderId);
         $guest     = $this->getCustomerIsGuest();
        if (!$guest) {
            return $order->getCustomerEmail();
        }
    }
    public function getSellerOrderTotals()
    {
        $sellerproductsarray = $this->getSellerProductCollection();
        $ordertotals = 0;
        $productscollection = $this->loadProductCollection();
        foreach ($productscollection as $product) {
            $productData    = $product->getData();
            if (in_array($productData['product_id'], $sellerproductsarray)) {
                $ordertotals += $productData['base_row_total_incl_tax'];
            }
        }
        return $ordertotals;
    }
    
    /**
     * @return Product Collection
     *
     *
     */
    public function getSellerId()
    {
        $entity_id = $this->getRequest()->getParam('entity_id');
         $sellerorder = $this->_sellerorderFactory->create();
         $sellerorder->load($entity_id);
         return $sellerorder->getSellerId();
    }
    public function getSellerorderInvoice()
    {
        $orderId  = $this->getOrderId();
        $sellerId = $this->getSellerId();
        return $this->_sellerOrderInvoice->getSellerOrderInvoice($sellerId, $orderId);
    }
    public function getSellerProductCollection()
    {
        $orderId            = $this->getOrderId();
        $sellerId           = $this->getSellerId();
        $sellerproducts         = $this->_sellerOrder->getSellerProducts($sellerId, $orderId);
        $sellerproductsarray = [];
        foreach ($sellerproducts as $sellerproduct) {
            $sellerproductsarray[] = $sellerproduct['product_id'];
        }
        return $sellerproductsarray;
    }
    public function getSellerShipping()
    {
        $orderId            = $this->getOrderId();
        $sellerId           = $this->getSellerId();
        $sellerproducts         = $this->_sellerOrder->getSellerProducts($sellerId, $orderId);
        $sellerproductsarray = [];
		$shipping = NULL;
        foreach ($sellerproducts as $sellerproduct) {
            $shipping = $sellerproduct['shipping'];
			break;
        }
        return $shipping;
    }
    public function getProductCollection()
    {
        $sellerproductsarray = $this->getSellerProductCollection();
        $productsoutrput    = [];
        $productscollection = $this->loadProductCollection();
        foreach ($productscollection as $product) {
            $productData    = $product->getData();
            if (in_array($productData['product_id'], $sellerproductsarray)) {
                $productsoutrput[] = [
                                        'name' => $productData['name'],
                                        'sku'  => $productData['sku'],
                                        'original_price'  => $productData['original_price'],
                                        'price'  => $productData['price'],
                                        'qty_ordered'  => $productData['qty_ordered'],
                                        'tax_amount'  => $productData['tax_amount'],
                                        'base_price_incl_tax'  => $productData['base_price_incl_tax'],
                                        'tax_percent'  => $productData['tax_percent'],
                                        'discount_amount'  => $productData['discount_amount'],
                                        'base_row_total_incl_tax'  => $productData['base_row_total_incl_tax'],
                                    ];
            }
        }
        return $productsoutrput;
    }
    
    /**
     * @return Shipping Address
     *
     *
     */
    public function getShippingAddress()
    {
        $orderId=$this->getOrderId();
        $orderData=$this->order->load($orderId);
		if($orderData->getShippingAddress()) {
        return $orderData->getShippingAddress()->getData();
		}
    }
    
    /**
     * @return Billing Address
     *
     *
     */
    public function getBillingAddress()
    {
        $orderId=$this->getOrderId();
        $orderData=$this->order->load($orderId);
        return $orderData->getBillingAddress()->getData();
    }
    
    /**
     * @return Payment Method
     *
     *
     */
    public function getPaymentMethod()
    {
        $orderId=$this->getOrderId();
        $orderData=$this->order->load($orderId);
        return $orderData->getPayment()->getMethodInstance()->getTitle();
    }
    
    /**
     * @return Country
     *
     *
     */
    public function getCountryByCode($countryId)
    {
        $country = $this->countryFactory->create()->loadByCode($countryId);
        return $country->getName();
    }
    
    /**
     * Currency Symbol
     *
     * @return Currency Symbol
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
    
    /**
     * Shipment View Url
     *
     * @return Shipment View Url
     */
    public function getShipmentUrl($order)
    {
        return $this->getUrl('marketplace/index/shipmentview', ['order_id' => $order]);
    }
    
    /**
     * Invoice View Url
     *
     * @return Invoice View Url
     */
    public function getInvoiceUrl($order)
    {
        return $this->getUrl('marketplace/index/invoiceview', ['order_id' => $order]);
    }
 
    /**
     * Currency Data
     *
     * @return Currency Data
     */
    public function getCurrencyData($price)
    {
        $currencySymbol = $this->priceHelper->currency($price, true, false);
        return $currencySymbol;
    }
}
