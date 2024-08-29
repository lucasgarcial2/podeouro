<?php
/**
 * Purpletree_Marketplace VendorContact
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

class VendorContact extends Action
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
   public $dataHelper;
   public $resultForwardFactory;
   public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory       =       $resultPageFactory;
        $this->customer                 =       $customer;
        $this->coreRegistry             =       $coreRegistry;
        $this->dataHelper               =       $dataHelper;
        $this->resultForwardFactory     =       $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {

        $data = $this->getRequest()->getPostValue();
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if (isset($data['referral']) && isset($data['sellerId'])) {
            $this->coreRegistry->register('referral', $data['referral']);
            $this->coreRegistry->register('seller_id', $data['sellerId']);
        } else {
            return $this->_redirect('marketplace/index/orderview');
        }
        $this->_resultPage = $this->_resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Contact Seller'));
        return $this->_resultPage;
    }
}
