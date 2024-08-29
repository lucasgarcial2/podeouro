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
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class ChangeSellerStatus extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order $order
     * @param PageFactory $resultPageFactory
     */
    public $customer;
    public $_sellerorder;
    public $storeManager;
    public $dataHelper;
    public $storeDetails;
    public $_sellerorderCollectionFactory;
    public $_sellerorderFactory;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Model\SellerorderFactory $sellerorderFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerorder,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory $sellerorderCollectionFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper
    ) {
        $this->customer                      = $customer;
         $this->_sellerorder                 = $sellerorder;
        $this->storeManager                  = $storeManager;
        $this->dataHelper                    = $dataHelper;
        $this->storeDetails                  = $storeDetails;
        $this->_sellerorderCollectionFactory = $sellerorderCollectionFactory;
        $this->_sellerorderFactory           = $sellerorderFactory;
        $this->resultForwardFactory          = $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId = $this->customer->getCustomer()->getId();
        $sellerId = $this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        $seller_status  = $this->getRequest()->getParam('seller_status');
        $id  = $this->getRequest()->getParam('order_id');
        if (!$id || $sellerId=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $sellerorderr = $this->getOrderCollection($id);
        if (!$sellerorderr) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $entity_ids  = $this->_sellerorder->getEntityIdfromOrderId($customerId, $id);

        foreach ($entity_ids as $idd) {
            $sellerorder = $this->_sellerorderFactory->create();
            $sellerorder->load($idd['entity_id']);
            $sellerorder->setOrderStatus($seller_status);
            $sellerorder->save();
        }
        $this->messageManager->addSuccess(__('Seller Invoice Status Updated Successfully.'));
        return $this->_redirect('marketplace/index/orderview/order_id/'.$id);
    }
    
     /**
      *
      *
      * @return Order Collection
      */
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
