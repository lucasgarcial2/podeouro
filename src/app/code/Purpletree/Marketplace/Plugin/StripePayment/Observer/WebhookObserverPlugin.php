<?php
/**
 * Purpletree_Marketplace
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

namespace Purpletree\Marketplace\Plugin\StripePayment\Observer;

//use StripeIntegration\Payments\Observer\WebhooksObserver;
use Magento\Framework\Event\Observer;
//use StripeIntegration\Payments\Model\Config;
use Purpletree\Marketplace\Helper\Data as MarketplaceHelper;
use Magento\Framework\Exception\LocalizedException;
use Purpletree\Marketplace\Model\PaymentsFactory;
use Psr\Log\LoggerInterface;
use Purpletree\Marketplace\Model\ResourceModel\Payments as PaymentResource;

class WebhookObserverPlugin
{
    private $_stripeConfig;

    private $_marketplaceHelper;

    private $_paymentResource;

    private $_paymentFactory;

    private $_logger;

    public function __construct(
       // Config $stripeConfig,
        MarketPlaceHelper $marketplaceHelper,
        PaymentsFactory $paymentFactory,
        PaymentResource $paymentResource,
        LoggerInterface $logger
        ){
       // $this->_stripeConfig =  $stripeConfig;
        $this->_marketplaceHelper = $marketplaceHelper;
        $this->_paymentFactory = $paymentFactory;
        $this->_paymentResource = $paymentResource;
        $this->_logger = $logger;
    }
    public function afterExecute(WebhooksObserver $subject, $result, Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $arrEvent = $observer->getData('arrEvent');
        $stdEvent = $observer->getData('stdEvent');
        $object = $observer->getData('object');
        $paymentMethod = $observer->getData('paymentMethod');
        $isAsynchronousPaymentMethod = false;
        switch ($eventName)
        {
            // Creates Stripe transfer for an order when the payment is captured from the Stripe dashboard
            case 'stripe_payments_webhook_charge_succeeded':
                if ($subject->wasCapturedFromAdmin($object)) {
                    break;
                }
                $amountCaptured = ($object["captured"] ? $object['amount_captured'] : 0);
                
                if($amountCaptured <= 0) {
                    break;
                }
                $order = $subject->webhooksHelper->loadOrderFromEvent($arrEvent);
                if (empty($object['payment_intent'])) {
                    break;
                }

                $payableAmount = $this->_marketplaceHelper->calculatePayableAmountToSeller($order);
                if(!$this->_marketplaceHelper->isSplitPaymentMethodEnabled('stripe')
                    || !$payableAmount) {
                    break;
                }
                $currency = $order->getOrderCurrencyCode();
                $incrementId = $order->getIncrementId();
                $this->_logger->info("payableAmount:", ['payableAmount'=> $payableAmount]);
                foreach ($payableAmount as $sellerId => $amount) {
                    $storeDetails = $this->_marketplaceHelper->getStoreDetails($sellerId);
                    $stripeAccountId = $storeDetails['stripe_account_id'] ?? '';
                    if(!$stripeAccountId) {
                        continue;
                    }
                    $stripeAmount = $subject->paymentsHelper->convertMagentoAmountToStripeAmount($amount["payable_amount"], $currency);
                
                    try {
                        $params = [
                            'amount' => $stripeAmount,
                            'currency' => strtolower($currency),
                            'destination' => $stripeAccountId,
                            'transfer_group' => $incrementId,
                        ];
                        $this->_logger->info("params:", ['params'=> $params]);
                        $transfer = $this->_stripeConfig
                            ->getStripeClient()
                            ->transfers
                            ->create($params);
                        $this->_logger->info("transfer:", ['transfer'=> $transfer]);
                        if(property_exists($transfer, "error")) {
                            throw new LocalizedException(__('Transfer Failed to Seller %1', $sellerId));
                        }
                        $sellerPayment = $this->_paymentFactory->create();
                        $sellerPayment->setData([
                            "seller_id" => $sellerId,
                            "transaction_id" => $transfer->id,
                            "amount" => $amount["payable_amount"],
                            "payment_mode" => "Online",
                            "status" => "enabled"
                        ]);
                        $this->_paymentResource->save($sellerPayment);
                    } catch(LocalizedException $e) {
                        $this->_logger->error("Error:", ['exception'=> $e]);
                        continue;
                    }
                }
                break;
            default:
                # code...
                break;
        }
    }
}