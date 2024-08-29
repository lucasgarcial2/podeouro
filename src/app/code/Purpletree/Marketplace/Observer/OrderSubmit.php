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
use Magento\Sales\Model\Order\Address\Renderer;

class OrderSubmit implements ObserverInterface
{
    /**
        \Purpletree\Marketplace\Helper\Data
        \Magento\Sales\Api\Data\OrderInterface
        \Magento\Catalog\Model\ProductRepository
        \Purpletree\Marketplace\Model\SellerorderFactory
        \Magento\Framework\Stdlib\DateTime\DateTime
     */

     protected $dataHelper;
     protected $messageManager;
     protected $productRepository;
     protected $_sellerorderFactory;
     protected $_seller;
     protected $_checkoutSession;
     protected $_date;
     protected $scopeConfig;
     protected $addressRenderer;
     protected $paymentHelper;
     protected $_priceHelper;

    public function __construct(
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Purpletree\Marketplace\Model\SellerorderFactory $sellerorderFactory,
        \Magento\Customer\Model\Customer $seller,
			\Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        Renderer $addressRenderer
    ) {
        $this->dataHelper               =       $dataHelper;
        $this->messageManager           =       $messageManager;
        $this->productRepository        =       $productRepository;
        $this->_sellerorderFactory      =       $sellerorderFactory;
        $this->_seller                  =       $seller;
			$this->_checkoutSession = $checkoutSession;
        $this->_date                    =       $date;
        $this->scopeConfig              =       $scopeConfig;
        $this->addressRenderer          =       $addressRenderer;
        $this->paymentHelper            =       $paymentHelper;
        $this->_priceHelper             =       $priceHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnable = $this->dataHelper->getGeneralConfig('general/enabled');
        if ($moduleEnable) {
            $order  = $observer->getEvent()->getOrder();
            $seller_orders = [];
			if($order->getShippingMethod() == 'purpletreetablerate_bestway') {
				if($this->_checkoutSession->getSellerShipping()) {
					$sellershipping = $this->_checkoutSession->getSellerShipping();
					$this->_checkoutSession->unsSellerShipping();
				}
			}
            foreach ($order->getAllVisibleItems() as $items) {
                $product = $this->productRepository->getById($items['product_id']);
                $seller_id = $product->getData('seller_id');
                if ($seller_id) {
					$sellershipp = NULL;
					if(isset($sellershipping)) {
						if(isset($sellershipping[$seller_id])) {
							$sellershipp = $sellershipping[$seller_id];
						}
					}
                    $sellerorder = $this->_sellerorderFactory->create();
                    $data = ['order_id'      => $order->getId(),
                                   'product_id'   => $items['product_id'],
								   'shipping'	  => $sellershipp,
                                   'seller_id'    => $product->getData('seller_id'),
                                   'order_status' => $order->getStatus(),
                                   'updated_at'   => $this->_date->date(),
                                   'created_at'   => $this->_date->date()
                                   ];
                    $sellerorder->SetData($data);
                    $sellerorder->save();
                    $seller_orders[$seller_id][] = $items;
                }
            }
            //Email to Seller
            $identifier = 'vendor_order';
            if (!empty($seller_orders)) {
                foreach ($seller_orders as $seller_idd => $items) {
                    $sellerObj = $this->_seller->load($seller_idd);
                    try {
                        $totalsss           = 0;
                        $productshtml       = '';
                        foreach ($items as $item) {
                             $optionsHtml       = '';
                             $getItemOptions    = $this->getItemOptions($item);
                            if (!empty($getItemOptions)) {
                                $optionsHtml .= '<dl class="item-options">';
                                foreach ($getItemOptions as $option) :
                                    $optionsHtml .= '<dt><strong><em>'.$option["label"].'</em></strong></dt><dd>'.nl2br($option['value']).'</dd>';
                                endforeach;
                                $optionsHtml .= '</dl>';
                            }
                             $totalsss += $item->getRowTotalInclTax();
                             $productshtml .= '<tbody><tr><td class="item-info has-extra" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc"><p class="product-name" style="margin-top:0;margin-bottom:5px;font-weight:700">'.$item->getName().'</p><p class="sku" style="margin-top:0;margin-bottom:10px">SKU: '.$item->getSku().'</p>'.$optionsHtml.'
						 </td><td class="item-qty" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc;text-align:center">'.$item->getQtyOrdered().'</td><td class="item-price" style="font-family:\'Open Sans\',\'Helvetica Neue\',Helvetica,Arial,sans-serif;vertical-align:top;padding:10px;border-top:1px solid #ccc;text-align:right"><span class="price">'.$this->getCurrencyData($item->getRowTotalInclTax()).'</span></td></tr></tbody>';
                        }
                        $emailTemplateVariables                             = [];
                        $emailTemplateVariables['order']                    = $order;
                        $emailTemplateVariables['productshtml']             = $productshtml;
                        $emailTemplateVariables['productstotalhtml']        = $this->getCurrencyData($totalsss);
                        $emailTemplateVariables['payment_html']             = $this->getPaymentHtml($order);
                        $emailTemplateVariables['formattedShippingAddress'] = $this->getFormattedShippingAddress($order);
                        $emailTemplateVariables['formattedBillingAddress']  = $this->getFormattedBillingAddress($order);
                        $emailTemplateVariables['seller_name']              = $sellerObj->getName();
                          $sender = [
                          'name' => $this->getStoreName(),
                          'email' =>$this->getStoreEmail()
                          ];
                          $receiver = [
                          'name' =>$sellerObj->getName(),
                          'email' => $sellerObj->getEmail()
                          ];
                               $this->dataHelper->yourCustomMailSendMethod(
                                   $emailTemplateVariables,
                                   $sender,
                                   $receiver,
                                   $identifier
                               );
                    } catch (\Exception $e) {
                        $this->messageManager
                        ->addError(__('Not Able to send Email to Seller'.$e->getMessage()));
                    }
                }
            }
            //Email to Seller
        }
    }
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
    private function getFormattedBillingAddress($order)
    {
        return $this->addressRenderer->format($order->getBillingAddress(), 'html');
    }
        /**
         * @param Order $order
         * @return string|null
         */
    private function getFormattedShippingAddress($order)
    {
        return $order->getIsVirtual()
            ? null
            : $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }
        /**
         * Returns payment info block as HTML.
         *
         * @param \Magento\Sales\Api\Data\OrderInterface $order
         *
         * @return string
         */
    private function getPaymentHtml(\Magento\Sales\Api\Data\OrderInterface $order)
    {
        return $this->paymentHelper->getInfoBlockHtml(
            $order->getPayment(),
            $order->getStoreId()
        );
    }
    private function getItemOptions($orderitem)
    {
        $result = [];
        if ($options = $orderitem->getProductOptions()) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }
    private function getSku($item)
    {
        if ($item->getOrderItem()->getProductOptionByCode('simple_sku')) {
            return $item->getOrderItem()->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }
    private function getCurrencyData($price)
    {
        return $this->_priceHelper->currency($price, true, false);
    }
}
