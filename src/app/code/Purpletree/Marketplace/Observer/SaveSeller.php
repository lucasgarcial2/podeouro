<?php

/**
 * Purpletree_Marketplace SaveSeller
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

namespace Purpletree\Marketplace\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class SaveSeller implements ObserverInterface
{

    /**
     * @param \Magento\Customer\Api\CustomerRepositoryInterface
     * @param \Magento\Customer\Model\CustomerFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\App\Config\ScopeConfigInterface
     * @param \Magento\Framework\Mail\Template\TransportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface
     * @param \Magento\Customer\Model\ResourceModel\CustomerFactory
     */
    public $sellerModel;
    public $indexerRegistry;
    public $processdata;
    public $customerFactory;
    public $customerfactorysave;
    public $dataHelper;
    public $messageManager;
    public $attributeSetManagement;
    public $_eavTypeFactory;
    public $attributeSetFactory;
    public $scopeConfig;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\Seller $sellerModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Purpletree\Marketplace\Helper\Processdata $processdata,
        \Magento\Eav\Model\AttributeSetManagement $attributeSetManagement,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerfactorysave
    ) {
        $this->sellerModel       =       $sellerModel;
        $this->indexerRegistry       =       $indexerRegistry;
        $this->processdata          =       $processdata;
        $this->customerFactory          =       $customerFactory;
        $this->customerfactorysave      =       $customerfactorysave;
        $this->dataHelper               =       $dataHelper;
         $this->messageManager = $messageManager;
        $this->attributeSetManagement          =      $attributeSetManagement;
        $this->_eavTypeFactory       =      $eavTypeFactory;
        $this->attributeSetFactory   =      $attributeSetFactory;
        $this->scopeConfig              =       $scopeConfig;
        $this->processdata->getProcessingdata();
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if ($moduleEnable) {
            $accountController = $observer->getAccountController();
            $customer = $observer->getCustomer();
            $customerId = $customer->getId();
            $request = $accountController->getRequest();
            $is_seller = $request->getParam('is_seller');
            if ($is_seller==1) {
                try {
                    $sellerReq=$this->dataHelper
                    ->getGeneralConfig('general/seller_approval_required');
                    $isseller=$sellerReq==0 ? 1 : 0;
                    $store_url =strtolower($request->getParam('store_url'));
                    $sellersave = $this->sellerModel;
                    $sellersave->setSellerId($customerId);
                    $sellersave->setStoreUrl($store_url);
                    $sellersave->setStatusId($isseller);
                    $sellersave->save();
                    $this->saveattributeValue($is_seller, $customerId);
                    $message=' ';
                    $message1='Seller Registration successfull.Please enter store details.';
                    if ($sellerReq) {
                        $message='New Seller registered Successfully.';
                    } else {
                        $message='New Seller register. Approve the seller';
                        $this->messageManager->addNotice(__('Approval Pending by Admin.'));
                    }
                        $this->messageManager->addSuccess(__($message1));
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\RuntimeException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\Exception $e) {
                    $messagecatch = 'Something went wrong.';
                }
                try {
                    $this->mailToSeller($message, $store_url, $customer);
                    $this->mailToAdmin($message, $store_url, $customer);
                } catch (\Magento\Framework\Exception\LocalizedException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\RuntimeException $e) {
                    $messagecatch = $e->getMessage();
                } catch (\Exception $e) {
                    $messagecatch = 'Something went wrong.';
                }
            }
        }
    }
    private function reIndexCustomer($customerId)
    {
        $indexerIds = ['customer_grid'];
            $startTime = microtime(true);
        foreach ($indexerIds as $indexerId) {
            try {
                $indexer = $this->indexerRegistry->get($indexerId);
                $indexer->reindexAll($customerId);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager
                ->addError($indexer->getTitle() . ' indexer process unknown error:', $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager
                ->addException($e, __("We couldn't reindex data because of an error.".$e->getMessage()));
            }
        }
    }
        
    /**
     * Save Customer Attribute
     *
     *
     */
    private function saveattributeValue($value, $customerId)
    {
        $customer = $this->customerFactory->create();
        $customerData = $customer->getDataModel();
        $customerData->setId($customerId);
        $customerData->setCustomAttribute('is_seller', $value);
        $customer->updateData($customerData);
        $customerResource = $this->customerfactorysave->create();
        $customerResource->saveAttribute($customer, 'is_seller');
        $entityTypeCode = 'catalog_product';
                    $entityType     = $this->_eavTypeFactory->create()
                    ->loadByCode($entityTypeCode);
                    $defaultSetId   = $entityType->getDefaultAttributeSetId();
                    $datas = [
                        'attribute_set_name'    => "Default_seller_".$customerId,
                        'entity_type_id'        => $entityType->getId()
                    ];
                    $attributeSet = $this->attributeSetFactory->create();
                    $attributeSet->setData($datas);
                    $this->attributeSetManagement
                    ->create($entityTypeCode, $attributeSet, $defaultSetId);
                    $this->reIndexCustomer($customerId);
    }
    
    /**
     *   Registration Email
     *
     *
     */
    private function mailToSeller($message, $store_url, $customer)
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
            $this->messageManager
            ->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    private function mailToAdmin($message, $store_url, $customer)
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
            $this->messageManager
            ->addError(__('Not Able to send Email'.$e->getMessage()));
        }
    }
    
    /**
     * Admin Store Email
     *
     * @return  Admin Store Email
     */
    private function getStoreEmail()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    private function getStoreName()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
