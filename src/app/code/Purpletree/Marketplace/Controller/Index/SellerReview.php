<?php
/**
 * Purpletree_Marketplace SellerReview
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

class SellerReview extends Action
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
    public $coreRegistry;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;
    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Helper\Processdata $processdata,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory       =       $resultPageFactory;
        $this->customer                 =       $customer;
        $this->coreRegistry             =       $coreRegistry;
        $this->storeDetails                 =       $storeDetails;
        $this->dataHelper               =       $dataHelper;
        $this->resultForwardFactory     =       $resultForwardFactory;
        $processdata->getProcessingdata();
        parent::__construct($context);
    }

    public function execute()
    {
        $sellerId=$this->getRequest()->getParam('sellerid');
        $seller=$this->storeDetails->isSeller($sellerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
       // $reviewEnable=$this->dataHelper->getGeneralConfig('seller_review/seller_review_enabled');
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->coreRegistry->register('seller_Id', $sellerId);
        if ($this->customer->isLoggedIn()) {
            $this->coreRegistry->register('customer_Id', $this->customer->getCustomer()->getId());
        } else {
            $this->coreRegistry->register('customer_Id', '');
        }
        $this->_resultPage = $this->_resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Seller Review'));
        return $this->_resultPage;
    }
}
