<?php
/**
 * Purpletree_Marketplace ShippingPolicy
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

use \Magento\Framework\App\Action\Action;
use \Magento\Customer\Model\Session as CustomerSession;

class ShippingPolicy extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Registry
     * @param \Magento\Framework\App\Request\Http
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */

    public $_resultPageFactory;
    public $customer;
    public $storeManager;
    public $coreRegistry;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory       =       $resultPageFactory;
        $this->customer                 =       $customer;
        $this->storeManager             =       $storeManager;
        $this->coreRegistry             =       $coreRegistry;
        $this->storeDetails                 =       $storeDetails;
        $this->dataHelper               =       $dataHelper;
        $this->resultForwardFactory     =       $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $sellerId=$this->getRequest()->getParam('sellerid');
        $seller=$this->storeDetails->isSeller($sellerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $this->coreRegistry->register('seller_id', $sellerId);
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->_resultPage = $this->_resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Store Shipping Policy'));
        return $this->_resultPage;
    }
}
