<?php
/**
 * Purpletree_Marketplace InvoiceSave
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

class InvoiceSave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Sales\Api\OrderRepositoryInterface
     * @param \Magento\Sales\Model\Service\InvoiceService
     * @param \Magento\Framework\DB\Transaction
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $customer;
    public $storeManager;
    public $storeDetails;
    public $resultForwardFactory;
    public $dataHelper;
    public $orderRepository;
    public $_sellerorderCollectionFactory;
    public $_sellerorderinvoiceFactory;
    public $_date;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Purpletree\Marketplace\Model\SellerorderinvoiceFactory $sellerorderinvoiceFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory $sellerorderCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        Context $context
    ) {
        $this->customer                      = $customer;
        $this->storeManager                  = $storeManager;
        $this->storeDetails                  = $storeDetails;
        $this->resultForwardFactory          = $resultForwardFactory;
        $this->dataHelper                    = $dataHelper;
        $this->orderRepository               = $orderRepository;
        $this->_sellerorderCollectionFactory = $sellerorderCollectionFactory;
        $this->_sellerorderinvoiceFactory    = $sellerorderinvoiceFactory;
        $this->_date                         = $date;
        parent::__construct($context);
    }
    
    public function execute()
    {
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        $sellerId   = $this->customer->getCustomer()->getId();
        $seller         = $this->storeDetails->isSeller($sellerId);
        $moduleEnable   = $this->dataHelper->getGeneralConfig('general/enabled');
        $manageOrder    = $this->dataHelper->getGeneralConfig('general/allow_seller_manage_order');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        
        if ($seller=='' || !$moduleEnable || !$manageOrder) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $comment = $data['invoice_comment'];
                $orderId = $data['order_id'];
                    $sellerorderr = $this->getOrderCollection($orderId);
                if (!$sellerorderr) {
                    $resultForward = $this->resultForwardFactory->create();
                    return $resultForward->forward('noroute');
                }
                $order = $this->orderRepository->get($orderId);
                    $invoiceData = $this->_sellerorderinvoiceFactory->create();
                     $data = [
                           'order_id'     => $orderId,
                           'seller_id'    => $sellerId,
                           'updated_at'   => $this->_date->date(),
                           'comment'   	  => $comment,
                           'created_at'   => $this->_date->date()
                           ];
                     $invoiceData->SetData($data);
                     $invoiceData->save();
                     $this->messageManager->addSuccess(__('Seller Invoice created successfully'));
                     return $this->_redirect('marketplace/index/orderview/order_id/'.$orderId);
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the details'));
            }
        }
    }
    public function getOrderCollection($id)
    {
        $collectiossn = $this->_sellerorderCollectionFactory->create();
        $sellerId     = $this->customer->getCustomer()->getId();
        foreach ($collectiossn as $dddd) {
            if ($sellerId == $dddd->getSellerId()) {
                if ($id  == $dddd->getOrderId()) {
                    return true;
                }
            }
        }
        return false;
    }
}
