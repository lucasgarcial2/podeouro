<?php
/**
 * Purpletree_Marketplace InvoiceGenerate
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
class InvoiceGenerate extends \Magento\Framework\View\Element\Template
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
      public $coreRegistry;
      public $countryFactory;
      public $currency;
      public $_sellerOrder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Pricing\Helper\Data $currency,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerOrder,
        array $data = []
    ) {
        $this->order                = $order;
        $this->coreRegistry         = $coreRegistry;
        $this->countryFactory       = $countryFactory;
        $this->currency             = $currency;
        $this->_sellerOrder         = $sellerOrder;
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
    public function getSellerOrderStatus()
    {
        $orderId    = $this->getOrderId();
        $sellerId   = (int) $this->coreRegistry->registry('sellerId');
        $ddd        = $this->_sellerOrder->getSellerStatus($sellerId, $orderId);
        return $ddd['order_status'];
    }
    /**
     * @return Order Collection
     *
     *
     */
    public function getOrderCollection()
    {
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $orderData=$order->getData();
            if ($orderData['entity_id']==$id) {
                return $order->getData();
            }
        }
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
        $productsoutrput     = [];
        $productscollection  = $this->loadProductCollection();
        foreach ($productscollection as $product) {
            $productData    = $product->getData();
            if (in_array($productData['product_id'], $sellerproductsarray)) {
                $productsoutrput[] = [
                                        'name'              => $productData['name'],
                                        'sku'               => $productData['sku'],
                                        'price'                 => $productData['price'],
                                        'qty_ordered'       => $productData['qty_ordered'],
                                        'tax_amount'        => $productData['tax_amount'],
                                        'discount_amount'   => $productData['discount_amount'],
                                        'base_row_total_incl_tax'             => $productData['base_row_total_incl_tax'],
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
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $productCollection = $order->load($id);
            $data=$productCollection->getShippingAddress();
			if($data) {
            foreach ($data as $orderDatamodel) {
                return($orderDatamodel);
            }
			}
        }
    }
    
    /**
     * @return Billing Address
     *
     *
     */
    public function getBillingAddress()
    {
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $productCollection = $order->load($id);
            $data=$productCollection->getBillingAddress();
            foreach ($data as $orderDatamodel) {
                return($orderDatamodel);
            }
        }
    }
    
    /**
     * @return Payment Method
     *
     *
     */
    public function getPaymentMethod()
    {
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $productCollection = $order->load($id);
            $data=$productCollection->getPayment()->getMethodInstance()->getTitle();
            return $data;
        }
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
    
    public function getCurrencyData($price)
    {
        return $this->currency->currency($price, true, false);
    }
}
