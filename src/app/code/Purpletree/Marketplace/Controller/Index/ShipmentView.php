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
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class ShipmentView extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order $order
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param PageFactory $resultPageFactory
     */
    public $resultPageFactory;
    public $customer;
    public $order;
    public $coreRegistry;
    public $storeManager;
    public $dataHelper;
    public $storeDetails;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $order,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
        $this->order = $order;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        $this->dataHelper               =       $dataHelper;
        $this->storeDetails                 =       $storeDetails;
        $this->resultForwardFactory     =       $resultForwardFactory;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $sellerId=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        if ($sellerId=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $id  = $this->getRequest()->getParam('order_id');
        $data=$this->getOrderCollection($id);
        $this->coreRegistry->register('id', $id);
        $this->_resultPage = $this->resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Order').' # '.$data['increment_id']);
        return $this->_resultPage;
    }
    
    /**
     *
     *
     * @return Order Collection
     */
    public function getOrderCollection($id)
    {
        $orderCollection=$this->order->getCollection();
        foreach ($orderCollection as $order) {
            $orderData=$order->getData();
            if ($orderData['entity_id']==$id) {
                return $order->getData();
            }
        }
    }
}
