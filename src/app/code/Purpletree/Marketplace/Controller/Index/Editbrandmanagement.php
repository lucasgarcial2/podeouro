<?php
/**
 * Purpletree_Marketplace Editbrandmanagement
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

class Editbrandmanagement extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Registry
     * @param \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */

    public $_resultPageFactory;
    public $moduleManager;
    public $customer;
    public $coreRegistry;
    public $_resultForwardFactory;
    public $storeManager;
    public $storeDetails;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
		\Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
		$this->moduleManager 					= $moduleManager;
        $this->customer = $customer;
        $this->coreRegistry = $coreRegistry;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->storeManager = $storeManager;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultForward = $this->_resultForwardFactory->create();
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
		if (!$this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand') || $seller=='' || !$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            /** @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
			 $objectManager 		     = \Magento\Framework\App\ObjectManager::getInstance();
			$this->optionSettingFactory   = $objectManager->create('\Amasty\ShopbyBase\Model\OptionSettingFactory');
            $brand = $this->optionSettingFactory->create();
            $brand->load($id);
            $this->coreRegistry->register('current_brand', $brand);
            $this->coreRegistry->register('current_customer_id', $this->customer->getId());
            $this->_resultPage = $this->_resultPageFactory->create();
            
            $this->_resultPage->getConfig()->getTitle()->set(__('Edit Brand'));
            return $this->_resultPage;
        }
        return $resultForward->forward('noroute');
    }
}
