<?php
/**
 * Purpletree_Marketplace PrintOrder
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

class PrintOrder extends \Magento\Framework\App\Action\Action
{
    /**
     * @param Context $context
     * @param \Magento\Customer\Model\Session $customer
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Order $order
     * @param PageFactory $resultPageFactory
     */
    public $resultPageFactory;
    public $customer;
    public $order;
    public $coreRegistry;
    public $storeManager;
    public $_resultPage;

    public function __construct(
        Context $context,
        CustomerSession $customer,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order $order,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
        $this->order = $order;
        $this->coreRegistry = $coreRegistry;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Customer order history
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->customer->isLoggedIn()) {
                $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
                $this->customer->authenticate();
        }
        $id  = $this->getRequest()->getParam('order_id');
        $data=$this->getOrderCollection($id);
        $this->coreRegistry->register('id', $id);
        $this->_resultPage = $this->resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Order').' # '.$data['increment_id'].' '.strtoupper($data['status']));
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
