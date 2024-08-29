<?php
/**
 * Purpletree_Marketplace OrderView
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

use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 */
class OrderView extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Sales\Model\Order
     * @param \Magento\Directory\Model\CountryFactory
     * @param \Magento\Framework\Registry
     * @param \Magento\Directory\Model\Currency
     * @param \Purpletree\Marketplace\Helper\Data
     * @param array $data
     */

     public $order;
     public $priceHelper;
     public $coreRegistry;
     public $countryFactory;
     public $currency;
     public $dataHelper;
     public $_sellerOrder;
     public $_sellerOrderInvoice;
     public $statusCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\Currency $currency,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerOrder,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorderinvoice $sellerOrderInvoice,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        $this->order                            = $order;
        $this->priceHelper                            = $priceHelper;
        $this->coreRegistry                     = $coreRegistry;
        $this->countryFactory                   = $countryFactory;
        $this->currency                         = $currency;
        $this->dataHelper                       = $dataHelper;
        $this->_sellerOrder                     = $sellerOrder;
        $this->_sellerOrderInvoice              = $sellerOrderInvoice;
          $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return Order ID
     *
     *
     */
 /**
  * Get status options
  *
  * @return array
  */
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
    public function getOrderId()
    {
        $result = (int) $this->coreRegistry->registry('id');
        return $result;
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
    public function getSellerOrderStatus2()
    {
        $orderId    = $this->getOrderId();
        $sellerId   = $this->coreRegistry->registry('sellerId');
        $ddd        = $this->_sellerOrder->getSellerStatus($sellerId, $orderId);
        return $ddd['order_status'];
    }
    public function getSellerOrderStatus()
    {
        $orderId    = $this->getOrderId();
        $sellerId   = $this->coreRegistry->registry('sellerId');
        $ddd        = $this->_sellerOrder->getSellerStatus($sellerId, $orderId);
        return $this->getStatusOptio($ddd['order_status']);
    }
    public function loadProductCollection()
    {
        $orderId            = $this->getOrderId();
        return $this->order->load($orderId)->getItemsCollection();
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
    public function getSellerorderInvoice()
    {
        $orderId  = $this->getOrderId();
        $sellerId = $this->coreRegistry->registry('sellerId');
        return $this->_sellerOrderInvoice->getSellerOrderInvoice($sellerId, $orderId);
    }
    public function getSellerProductCollection()
    {
        $orderId            = $this->getOrderId();
        $sellerId           = $this->coreRegistry->registry('sellerId');
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
        $sellerId           = $this->coreRegistry->registry('sellerId');
        $sellerproducts         = $this->_sellerOrder->getSellerProducts($sellerId, $orderId);
        $sellershipping = NULL;
        foreach ($sellerproducts as $sellerproduct) {
            $sellershipping = $sellerproduct['shipping'];
			break;
        }
        return $sellershipping;
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
                                        'price'  => $productData['price'],
                                        'qty_ordered'  => $productData['qty_ordered'],
                                        'tax_amount'  => $productData['tax_amount'],
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
     * Print Url
     *
     * @return Print Url
     */
    public function getPrintUrl($order)
    {
        return $this->getUrl('marketplace/index/printorder', ['order_id' => $order]);
    }
    
    /**
     * Allow Order
     *
     * @return Allow Order
     */
    public function manageOrder()
    {
        return $this->dataHelper->getGeneralConfig('general/allow_seller_manage_order');
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
