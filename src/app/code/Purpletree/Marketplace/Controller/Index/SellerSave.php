<?php
/**
 * Purpletree_Marketplace SellerSave
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
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class SellerSave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Model\Seller
     * @param \Magento\Customer\Model\CustomerFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \\Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Magento\Framework\Mail\Template\TransportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface
     * @param \Magento\Framework\App\Action\Context
     *
     */

    public $indexerRegistry;
    public $customer;
    public $attributeSetManagement;
    public $customerFactory;
    public $dataHelper;
    public $storeDetails;
    public $customerfactorysave;
    public $resultForwardFactory;
    public $sellerFactory;
    public $store;
    public $_eavTypeFactory;
    public $attributeSetFactory;
    public $scopeConfig;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Purpletree\Marketplace\Model\Seller $store,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerfactorysave,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Eav\Model\AttributeSetManagement $attributeSetManagement,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        Context $context
    ) {
        $this->indexerRegistry       =       $indexerRegistry;
        $this->customer              =      $customer;
        $this->attributeSetManagement          =      $attributeSetManagement;
        $this->customerFactory       =      $customerFactory;
        $this->dataHelper            =      $dataHelper;
        $this->storeDetails          =      $storeDetails;
        $this->customerfactorysave   =      $customerfactorysave;
        $this->resultForwardFactory  =      $resultForwardFactory;
        $this->sellerFactory         =      $sellerFactory;
        $this->store                 =      $store;
        $this->_eavTypeFactory       =      $eavTypeFactory;
        $this->attributeSetFactory   =      $attributeSetFactory;
        $this->scopeConfig           =      $scopeConfig;
        parent::__construct($context);
    }
    
    public function execute()
    {
        
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->getSellerIdByCustomerId($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $sellerReq=$this->dataHelper->getGeneralConfig('general/seller_approval_required');
        $data = $this->getRequest()->getPostValue();
        $is_seller=$sellerReq==0 ? 1 : 0;
        if (!$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if ($data) {
            if (isset($data['store_url'])) {
                    $samurl = sizeof($this->storeDetails->checkUniqueUrl($data['store_url']));
                if ($samurl != 0) {
                    $this->messageManager->addError(__('Store Url already exits. Choose different store url.'));
                    return $this->_redirect('customer/account/index');
                }
            }
            try {
                if ($data['again_seller']==0) {
                    $this->store->setSellerId($customerId);
                    $this->store->setStoreUrl($data['store_url']);
                    $this->store->setStatusId($is_seller);
                    $this->saveattributeValue(1, $customerId);
                    $this->store->save();
                    $entityTypeCode = 'catalog_product';
                    $entityType     = $this->_eavTypeFactory->create()->loadByCode($entityTypeCode);
                    $defaultSetId   = $entityType->getDefaultAttributeSetId();
                    $datas = [
                        'attribute_set_name'    => "Default_seller_".$customerId,
                        'entity_type_id'        => $entityType->getId()
                    ];
                    $attributeSet = $this->attributeSetFactory->create();
                    $attributeSet->setData($datas);
                    $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
                } else {
                    $storeid=$this->storeDetails->storeId($customerId);
                    $sellersave    = $this->sellerFactory->create();
                    $sellersave->load($storeid);
                    if ($data['agseller']==1) {
                        $sellersave->setStatusId($is_seller);
                        $this->saveattributeValue(1, $customerId);
                        $sellersave->save();
                    }
                    if ($sellerReq && $data['agseller']==1) {
                        $this->messageManager->addSuccess(__('Waiting for admin approval'));
                    }
                }
                    $message='';
                if ($sellerReq) {
                    $message='New Seller registered.';
                } else {
                    $message='New Seller registered. Approve the seller';
                }
                try {
                    $cuystomer = $this->customer->getCustomer();
                    $this->mailToAdmin($message, $data['store_url'], $cuystomer);
                    $this->mailToSeller($message, $data['store_url'], $cuystomer);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\RuntimeException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\Exception $e) {
                    $messagecatch = 'Something went wrong.';
                }
                    return $this->_redirect('marketplace/index/becomeseller');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the details'));
            }
        }
        return $this->_redirect('customer/account/index');
    }
    public function reIndexCustomer($customerId)
    {
        $indexerIds = ['customer_grid'];
        $startTime = microtime(true);
        foreach ($indexerIds as $indexerId) {
            try {
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll($customerId);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($indexer->getTitle() . ' indexer process unknown error:', $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __("We couldn't reindex data because of an error.".$e->getMessage()));
            }
        }
    }
    /**
     * Save Customer Attribute Value
     *
     * @return NULL
     */
    public function saveattributeValue($value, $customerId)
    {
        $customer = $this->customerFactory->create();
        $customerData = $customer->getDataModel();
        $customerData->setId($customerId);
        $customerData->setCustomAttribute('is_seller', $value);
        $customer->updateData($customerData);
        $customerResource = $this->customerfactorysave->create();
        $customerResource->saveAttribute($customer, 'is_seller');
         $this->reIndexCustomer($customerId);
    }
    
    /**
     *   Registration Email
     *
     *
     */
    public function mailToSeller($message, $store_url, $customer)
    {
        $customerFirstName = $customer->getFirstName();
        $customerLastName = $customer->getLastName();
        $customerEmail = $customer->getEmail();
        $identifier    = 'seller_registration_email';
        try {
            $emailTemplateVariables = [];
             $emailTemplateVariables['name'] = $customerFirstName;
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
            $emailTemplateVariables['store_url'] = $store_url;
            $emailTemplateVariables['message'] = $message;
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' => $customerFirstName,
            'email' =>$customerEmail
            ];
            $this->dataHelper->yourCustomMailSendMethod(
                $emailTemplateVariables,
                $sender,
                $receiver,
                $identifier
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    public function mailToAdmin($message, $store_url, $customer)
    {
         $customerFirstName = $customer->getFirstName();
         $customerLastName = $customer->getLastName();
         $customerEmail = $customer->getEmail();
        $identifier    = 'seller_registration_email';
        try {
            $emailTemplateVariables = [];
             $emailTemplateVariables['name'] = $this->getStoreName();
            $emailTemplateVariables['sellername'] = $customerFirstName.' '.$customerLastName;
            $emailTemplateVariables['selleremail'] = $customerEmail;
            $emailTemplateVariables['store_url'] = $store_url;
            $emailTemplateVariables['message'] = $message;
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $this->dataHelper->yourCustomMailSendMethod(
                $emailTemplateVariables,
                $sender,
                $receiver,
                $identifier
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    
    /**
     * Admin Store Email
     *
     * @return  Admin Store Email
     */
    public function getStoreEmail()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getStoreName()
    {
        return $this->scopeConfig->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
