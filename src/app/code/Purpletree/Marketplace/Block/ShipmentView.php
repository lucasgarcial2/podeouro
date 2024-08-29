<?php
/**
 * Purpletree_Marketplace ShipmentView
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
class ShipmentView extends \Magento\Framework\View\Element\Template
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

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\Order $order,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Directory\Model\Currency $currency,
        array $data = []
    ) {
        $this->order = $order;
        $this->coreRegistry = $coreRegistry;
        $this->countryFactory = $countryFactory;
        $this->currency = $currency;
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
    
    /**
     * @return Product Collection
     *
     *
     */
    public function getProductCollection()
    {
        $orderId=$this->getOrderId();
        return $this->order->load($orderId)->getItemsCollection();
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
     * Invoice View Url
     *
     * @return Invoice View Url
     */
    public function getInvoiceUrl($order)
    {
        return $this->getUrl('marketplace/index/invoiceview', ['order_id' => $order]);
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
}
