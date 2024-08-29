<?php
/**
 * Purpletree_Marketplace InvoiceView
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
class InvoiceView extends \Magento\Framework\View\Element\Template
{
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     */

     public $order;
     public $_priceHelper;
     public $coreRegistry;
     public $countryFactory;
     public $currency;
     public $_sellerOrder;
     public $orderRepository; 


    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerOrder,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        array $data = []
    ) {
        $this->order = $order;
        $this->_priceHelper = $priceHelper;
        $this->coreRegistry = $coreRegistry;
        $this->countryFactory = $countryFactory;
        $this->currency = $currency;
        $this->_sellerOrder                     = $sellerOrder;
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    /**
     * @return Order ID
     *
     *
     */
    public function getOrderId()
    {
        $result = (int) $this->coreRegistry->registry('id');
        return $result;
    }
    public function getOrderIncrementId()
    {
        $id = (int) $this->coreRegistry->registry('id');
        $order = $this->orderRepository->get($id);
        return $order->getIncrementId();
    }
    public function geInvoice()
    {
        $result = $this->coreRegistry->registry('invoice');
        return $result;
    }
    /**
     * @return Order Collection
     *
     *
     */
    public function getOrderCollection()
    {
        $orderId=$this->getOrderId();
        return $this->order->load($orderId)->getData();
    }
    public function loadProductCollection()
    {
        $orderId            = $this->getOrderId();
        return $this->order->load($orderId)->getItemsCollection();
    }
    public function getSellerOrderTotals()
    {
        $sellerproductsarray = $this->getSellerProductCollection();
        $ordertotals         = 0;
        $productscollection  = $this->loadProductCollection();
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
    public function getSellerProductCollection()
    {
        $orderId            = $this->getOrderId();
        $sellerId           = (int) $this->coreRegistry->registry('sellerId');
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
        $sellerId           = (int) $this->coreRegistry->registry('sellerId');
        $sellerproducts         = $this->_sellerOrder->getSellerProducts($sellerId, $orderId);
        $sellership = NULL;
        foreach ($sellerproducts as $sellerproduct) {
            $sellership = $sellerproduct['shipping'];
			break;
        }
        return $sellership;
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
     * Order Url
     *
     * @return OrderUrl
     */
    public function getOrderUrl($order)
    {
        return $this->getUrl('marketplace/index/orderview', ['order_id' => $order]);
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
    
    public function getCurrencyData($price)
    {
        $currencySymbol = $this->_priceHelper->currency($price, true, false);
        return $currencySymbol;
    }
}
