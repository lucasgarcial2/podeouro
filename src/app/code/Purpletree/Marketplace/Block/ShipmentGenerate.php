<?php
/**
 * Purpletree_Marketplace ShipmentGenerate
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
class ShipmentGenerate extends \Magento\Framework\View\Element\Template
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
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $orderData=$order->getData();
            if ($orderData['entity_id']==$id) {
                return $order->getData();
            }
        }
    }
    
    /**
     * @return Product Collection
     *
     *
     */
    public function getProductCollection()
    {
        $orderCollection=$this->order->getCollection();
        $id=$this->getOrderId();
        foreach ($orderCollection as $order) {
            $productCollection = $order->load($id);
            return $productCollection->getItemsCollection();
        }
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
    
    /**
     * Currency Symbol
     *
     * @return Currency Symbol
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
}
