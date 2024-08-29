<?php
/**
 * Purpletree_Marketplace Data
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

namespace Purpletree\Marketplace\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Purpletree\Marketplace\Model\CategorycommissionFactory;

class Data extends AbstractHelper
{

    const DEFAULT_ENABLED                  =   0;
    
    const XML_PATH_MARKETPLACE = 'purpletree_marketplace/';
    
    public $storeManager;
    public $transportBuilder;
    public $inlineTranslation;
    public $_collectionFactory;
    public $_sellerorderFactory;
    public $storeDetails;
    public $order;
    public $messageManager;
    public $_productMetadataInterface;
    public $_categoryCommissionFactory;
    public $temp_id;
    
    public function __construct(
        Context $context,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Purpletree\Marketplace\Model\SellerorderFactory $sellerorderFactory,
         \Magento\Sales\Model\Order $order,
        StoreManagerInterface $storeManager,
        CategorycommissionFactory $categoryCommissionFactory
    ) {
        $this->storeManager  = $storeManager;
        $this->transportBuilder     =       $transportBuilder;
        $this->inlineTranslation    =       $inlineTranslation;
         $this->_collectionFactory = $collectionFactory;
             $this->_sellerorderFactory    = $sellerorderFactory;
          $this->storeDetails           = $storeDetails;
           $this->order                  = $order;
           $this->messageManager = $messageManager;
          $this->_productMetadataInterface             =       $productMetadataInterface;
          $this->_categoryCommissionFactory             =       $categoryCommissionFactory;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function getGeneralConfig($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_MARKETPLACE . $code, $storeId);
    }
    public function isEnabled($storeId = null)
    {
        $data = $this->getGeneralConfig('/general/enabled', $storeId);
        if ($data == null) {
            $data = self::DEFAULT_ENABLED;
        }
        return $data;
    }
    public function yourCustomMailSendMethod($emailTemplateVariables, $senderInfo, $receiverInfo, $identifier)
    {
        $this->temp_id = $identifier;
        $this->inlineTranslation->suspend();
        $this->generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo);
        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $this->inlineTranslation->resume();
    }
    public function generateTemplate($emailTemplateVariables, $senderInfo, $receiverInfo)
    {
        $template =  $this->transportBuilder->setTemplateIdentifier($this->temp_id)
              ->setTemplateOptions(
                  [
                      'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                      'store' => $this->storeManager->getStore()->getId(),
                  ]
              )
              ->setTemplateVars($emailTemplateVariables)
              ->setFrom($senderInfo)
              ->addTo($receiverInfo['email'], $receiverInfo['name']);
        return $this;
    }
    public function getSellerCollection($productId, $sellerId)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('product_id');
        $collection->addFieldToFilter('entity_id', $productId);
        $collection->addAttributeToFilter('seller_id', $sellerId);
        $collection->load();
                        //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
        $collection->clear();
        $fromAndJoin = $collection->getSelect()->getPart('FROM');
        $updatedfromAndJoin = [];
        foreach ($fromAndJoin as $key => $index) {
            if ($key == 'stock_status_index') {
                $index['joinType'] = 'left join';
            }
            $updatedfromAndJoin[$key] = $index;
        }
        if (!empty($updatedfromAndJoin)) {
            $collection->getSelect()->setPart('FROM', $updatedfromAndJoin);
        }

        $where = $collection->getSelect()->getPart('where');
        $updatedWhere = [];
        foreach ($where as $key => $condition) {
				 if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                    $updatedWhere[] = $condition;
                }
            } else {
                if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                    $updatedWhere[] = $condition;
                }
            }
        }
        if (!empty($updatedWhere)) {
            $collection->getSelect()->setPart('where', $updatedWhere);
        }
        return $collection;
    }
    public function caclulateCommission($entity_ids,$order_id) {
        $order              = $this->order->load($order_id);
        $order_increment_id     = $order->getIncrementId();
        $orderProducts      = $order->getAllVisibleItems();
     $commissionPercnt   = $this->getGeneralConfig('general/commission');
        foreach ($entity_ids as $idd) {
            $sellerorder = $this->_sellerorderFactory->create();
            $sellerorder->load($idd['entity_id']);
            $seller_status = $sellerorder->getOrderStatus();
            if ($this->getGeneralConfig('general/orderstatus') == $seller_status) {
                foreach ($orderProducts as $item) {
                    if ($sellerorder->getProductId() == $item->getProductId()) {
                    //
					$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
					$productRepository = $objectManager->create('Magento\Catalog\Model\ProductRepository');
                        $product = $productRepository->getById($item->getProductId());
                        $sellerId = $product->getSellerId();
                        $final_cat_commison = '';
                        if ($sellerId!='') {
                                $productID = $item->getProductId();
                            // Category Commission
                            $catids = $product->getCategoryIds();
                                $commission_cat = [];
                                $catttt         = [];
                            if (!empty($catids)) {
					  $ccommissionFactory = $objectManager->create('\Purpletree\Marketplace\Model\CategorycommissionFactory');
                                $collection = $ccommissionFactory->create()->getCollection();
                                $collection->addFieldToFilter('category_id', $catids);
                                foreach ($collection as $dd) {
                                    $catttt[] = $dd->getCommission();
                                }
                            }
                            if (!empty($catttt)) {
                                $final_cat_commison = max($catttt);
                            }
                                $storeData = $this->getStoreDetails($sellerId);
                            if ($final_cat_commison != '') {
                                $commissionTotal = ($item->getPriceInclTax()*$final_cat_commison*$item->getQtyInvoiced())/100;
                            } // Category Commission
                            // Seller Commission
                            elseif ($storeData['store_commission'] != '') {
                                $commissionTotal = ($item->getPriceInclTax()*$storeData['store_commission']*$item->getQtyInvoiced())/100;
                            } // Seller Commission
                            // Config Global Commission
                            else {
                                $commissionTotal = ($item->getPriceInclTax()*$commissionPercnt*$item->getQtyOrdered())/100;
                            }
                            // Config Global Commission
                            $productPrice=$item->getPriceInclTax();
                            $productQuantity=$item->getQtyOrdered();
                            $productName=$product->getName();
                            $status = $seller_status;
                            if ($seller_status == 'complete') {
						$commissionsave = $objectManager->create('\Purpletree\Marketplace\Model\Commission');
						$commisssionmode = $objectManager->create('\Purpletree\Marketplace\Model\ResourceModel\Commission');
                                $dddd = $commisssionmode->getcommissionnnn($sellerId, $order_increment_id, $productID);
                                if ($dddd && isset($dddd['entity_id']) && $dddd['entity_id'] != '') {
									$ccommisssionmodeFactory = $objectManager->create('\Purpletree\Marketplace\Model\CommissionFactory');
                                    $commissionsavea  = $ccommisssionmodeFactory->create();
                                    $commissionsave  = $commissionsavea->load($dddd['entity_id']);
                                }
                                $commissionsave->setSellerId($sellerId);
                                $commissionsave->setOrderId($order_increment_id);
                                $commissionsave->setProductId($productID);
                                $commissionsave->setCommission($commissionTotal);
                                $commissionsave->setProductName($productName);
                                $commissionsave->setProductQuantity($productQuantity);
                                $commissionsave->setProductPrice($productPrice);
                                $commissionsave->setStatus($status);
                                try {
                                    $commissionsave->save();
                                } catch (\Exception $e) {
                                    $this->messageManager->addException($e, __('Something went wrong while saving the details'));
                                }
                            }
                        }
                    }
                }
            }
        }
    }
     public function getStoreDetails($sellerId)
    {
        return $this->storeDetails->getStoreDetails($sellerId);
    }

    /**
     * Check if payment method available for split payment
     *
     * @param string $methodCode
     * @return boolean
     */
    public function isSplitPaymentMethodEnabled($methodCode) {
        $enable = false;
        if($this->getGeneralConfig("split_payments/enable")) {
            $paymentMethods = $this->getGeneralConfig("split_payments/payment_methods");
            $paymentMethods = explode(",", $paymentMethods);
            if(in_array($methodCode, $paymentMethods)) {
                $enable =  true;
            }
        }
        return $enable;
    }

    /**
     * Calculate Payable Amount to Seller
     *
     * @param Magento\Sales\Model\Order $order
     * @return array
     */
    public function calculatePayableAmountToSeller($order)
    {
        $sellerOrders = $this->_sellerorderFactory->create()
            ->getCollection()
            ->addFieldToFilter("order_id", $order->getId());
        if(!$sellerOrders->getSize()) {
            return [];
        }
        $payableAmount = [];
        $orderProducts      = $order->getAllVisibleItems();
        $commissionPercent   = $this->getGeneralConfig('general/commission');
        foreach ($orderProducts as $item) {
            $product = $item->getProduct();
            $sellerId = $product->getSellerId();
            if(!$sellerId) {
                continue;
            }

            $final_cat_commison = '';
            // Category Commission
            $catids = $product->getCategoryIds();
            $commission_cat = [];
            $category_commision = [];
            if (!empty($catids)) {
                $categoryCommissionCollection = $this->_categoryCommissionFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('category_id', $catids);
                foreach ($categoryCommissionCollection as $categoryCommission) {
                    $category_commision[] = $categoryCommission->getCommission();
                }
            }
            if (!empty($category_commision)) {
                $final_cat_commison = max($category_commision);
            }
            $storeData = $this->getStoreDetails($sellerId);
            if ($final_cat_commison != '') {
                $commissionTotal = ($item->getPriceInclTax()*$final_cat_commison*$item->getQtyInvoiced())/100;
            } // Category Commission
            // Seller Commission
            elseif ($storeData['store_commission'] != '') {
                $commissionTotal = ($item->getPriceInclTax()*$storeData['store_commission']*$item->getQtyInvoiced())/100;
            } // Seller Commission
            // Config Global Commission
            else {
                $commissionTotal = ($item->getPriceInclTax()*$commissionPercnt*$item->getQtyOrdered())/100;
            }
            $productPayableAmount = ($item->getPriceInclTax()*$item->getQtyOrdered()) - $commissionTotal;
            if(isset($payableAmount[$sellerId])) {
                $productPayableAmount += $payableAmount[$sellerId]["payable_amount"];
            }
            $payableAmount[$sellerId] = [
              "payable_amount" => $productPayableAmount
            ];
        }
        return $payableAmount;
    }
}
