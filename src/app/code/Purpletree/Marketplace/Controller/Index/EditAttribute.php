<?php
/**
 * Purpletree_Marketplace EditAttribute
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

class EditAttribute extends Action
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
    public $customer;
    public $coreRegistry;
    public $_resultForwardFactory;
    public $storeManager;
    public $attributeFactory;
    public $storeDetails;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->_resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
        $this->coreRegistry = $coreRegistry;
        $this->_resultForwardFactory = $resultForwardFactory;
        $this->storeManager = $storeManager;
        $this->attributeFactory = $attributeFactory;
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
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $attribute_id = $this->getRequest()->getParam('id');
        if ($attribute_id) {
            /** @var $model \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
            $attribute = $this->attributeFactory->create();
            $attribute->load($attribute_id);
            $attribute_code = $attribute->getAttributeCode();
            if ($this->validateSeller($attribute->getAttributeCode()) == '') {
                return $resultForward->forward('noroute');
            }
            $this->coreRegistry->register('current_attribute', $attribute);
            $this->coreRegistry->register('current_customer_id', $this->customer->getId());
            $this->_resultPage = $this->_resultPageFactory->create();
            
            $this->_resultPage->getConfig()->getTitle()->set(__('Edit Attribute'));
            return $this->_resultPage;
        }
        return $resultForward->forward('noroute');
    }
    
    /**
     * Validate Seller
     *
     * @return Validate Seller
     */
    public function validateSeller($attribute_code)
    {
        
        $exploded_data = explode("_seller_", $attribute_code);
        if (isset($exploded_data[1])) {
            if ($this->sellerid() == end($exploded_data)) {
               array_pop($exploded_data);
			return implode('_seller_',$exploded_data);
            }
        }
    }
    
    /**
     * Seller ID
     *
     * @return Seller ID
     */
    public function sellerid()
    {
        return $this->customer->getId();
    }
}
