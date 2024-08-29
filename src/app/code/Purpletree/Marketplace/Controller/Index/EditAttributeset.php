<?php
/**
 * Purpletree_Marketplace NewAttribute
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

class EditAttributeset extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    
     public $attributeSet;
     public $_resultPageFactory;
     public $customer;
     public $storeManager;
      public $coreRegistry;
     public $storeDetails;
     public $dataHelper;
     public $_eavTypeFactory;
     public $resultForwardFactory;
     public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->attributeSet = $attributeSet;
        $this->_resultPageFactory = $resultPageFactory;
        $this->customer = $customer;
        $this->storeManager = $storeManager;
         $this->coreRegistry = $coreRegistry;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
        $this->_eavTypeFactory = $eavTypeFactory;
        $this->resultForwardFactory =       $resultForwardFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
         $setid = $this->getRequest()->getParam('id');
         $entityTypeCode = 'catalog_product';
            $entityType     = $this->_eavTypeFactory->create()->loadByCode($entityTypeCode);
            $attributeSellerId = '';
        if (isset($setid)) {
             /* @var $model \Magento\Eav\Model\Entity\Attribute\Set */
                $model = $this->attributeSet->setEntityTypeId($entityType->getId());
                $model->load($setid);
               $attributeSellerId = $this->validateSeller($model->getAttributeSetName());
        }
        if ($seller=='' || !$moduleEnable || $setid == '' || $attributeSellerId=='') {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $this->coreRegistry->register('current_customer_id', $this->customer->getId());
        $this->coreRegistry->register('attributesetid', $setid);
        $this->_resultPage = $this->_resultPageFactory->create();
        
        $this->_resultPage->getConfig()->getTitle()->set(__('Edit Attribute Set'));
        return $this->_resultPage;
    }
    public function validateSeller($attribute_code)
    {
        $exploded_data = explode("_seller_", $attribute_code);
        if (isset($exploded_data[1])) {
            if ($this->sellerid() == $exploded_data[1]) {
                  array_pop($exploded_data);
			return implode('_seller_',$exploded_data);
            }
        }
    }
    public function sellerid()
    {
        return $this->customer->getId();
    }
}
