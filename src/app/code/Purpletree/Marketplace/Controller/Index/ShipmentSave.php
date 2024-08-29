<?php
/**
 * Purpletree_Marketplace ShipmentSave
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
 
namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Customer\Model\Session as CustomerSession;

class ShipmentSave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface
     * @param \Magento\Sales\Model\Convert\Order
     * @param \Magento\Catalog\Model\Product
     * @param \Magento\Sales\Model\Order
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $customer;
    public $storeDetails;
    public $resultForwardFactory;
    public $dataHelper;
    public $orderTransaction;
    public $orderData;
    public $productRepository;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Model\Convert\Order $orderTransaction,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Purpletree\Marketplace\Model\Commission $commissionModel,
        \Magento\Sales\Model\Order $orderData,
        Context $context
    ) {
        $this->customer             =       $customer;
        $this->storeDetails             =       $storeDetails;
        $this->resultForwardFactory =       $resultForwardFactory;
        $this->dataHelper           =       $dataHelper;
        $this->orderTransaction     =       $orderTransaction;
        $this->orderData            =       $orderData;
        $this->productRepository    =       $productRepository;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $manageOrder=$this->dataHelper->getGeneralConfig('general/allow_seller_manage_order');
        $commissionPercnt=$this->dataHelper->getGeneralConfig('general/commission');
        $enableLowNotification=$this->dataHelper->getGeneralConfig('inventry/enable_low_notification');
        $lowStockQty=$this->dataHelper->getGeneralConfig('inventry/low_stock_qty');
        if ($seller=='' || !$moduleEnable || !$manageOrder) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $comment = $data['shipment_comment'];
            $orderId = $data['order_id'];
            $order = $this->orderData->load($orderId);
            if ($order->canShip()) {
                $shipment = $this->orderTransaction->toShipment($order);
                foreach ($order->getAllItems() as $orderItem) {
                    if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                        continue;
                    }
                    $qtyShipped = $orderItem->getQtyToShip();
                    $shipmentItem = $this->orderTransaction->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    $shipment->addItem($shipmentItem);
                }
                $shipment->register();
                $shipment->getOrder()->setIsInProcess(true);
                try {
                    $shipment->save();
                    $shipment->getOrder()->save();
                    $order->setState(\Magento\Sales\Model\Order::STATE_COMPLETE, true);
                    $order->setStatus('complete');
                    $order->addStatusHistoryComment($comment, false);
                    $order->save($order);
                    foreach ($order->getAllItems() as $item) {
                        $commissionTotal=($item->getPriceInclTax()*$commissionPercnt*$item->getQtyInvoiced())/100;
                        $orderID = $order->getRealOrderId();
                        $product = $this->productRepository->getById($item->getProductId());
                        $sellerId = $product->getSellerId();
                        if ($sellerId!='') {
                            $productID=$item->getProductId();
                            $productPrice=$item->getPriceInclTax();
                            $productQuantity=$item->getQtyInvoiced();
                            $productName=$product->getName();
                            $status=$order->getState();
                            $commissionsave = $this->commissionModel;
                            $commissionsave->setSellerId($sellerId);
                            $commissionsave->setOrderId($orderID);
                            $commissionsave->setProductId($productID);
                            $commissionsave->setCommission($commissionTotal);
                            $commissionsave->setProductName($productName);
                            $commissionsave->setProductQuantity($productQuantity);
                            $commissionsave->setProductPrice($productPrice);
                            $commissionsave->setStatus($status);
                            $commissionsave->save();
                        }
                    }
                    $this->messageManager->addSuccess(__('Shipment save successfully'));
                    return $this->_redirect('marketplace/index/orderview/order_id/'.$orderId);
                } catch (\Exception $e) {
                    $this->messageManager->addException($e, __('Something went wrong while saving the details'));
                }
            } else {
                $this->messageManager->addSuccess(__('Shipment can not save'));
                return $this->_redirect('marketplace/index/orderview/order_id/'.$orderId);
            }
        }
    }
}
