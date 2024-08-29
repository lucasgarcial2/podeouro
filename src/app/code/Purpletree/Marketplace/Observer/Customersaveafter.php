<?php
/**
 * Purpletree_Marketplace Customersaveafter
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
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;

class Customersaveafter implements ObserverInterface
{
      
    public $customerModel;
    public $indexerRegistry;
    public $messageManager;
    public $_request;
    public $dataHelper;
    public $categorycustom;
    public $_categoryFactory;
    public $actionStatus;
    public $productcollection;
    public $_sellerFactory;
    public $attributeSetFactory;
    public $sellercustom;
    public $_mediaDirectory;
    public $customerFactory;
    public $customerfactorysave;
    public $scopeConfig;
    public $_fileUploaderFactory;
    public $_eavTypeFactory;
    public $attributeSetManagement;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerfactorysave,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $sellercustom,
        \Purpletree\Marketplace\Model\ResourceModel\Category $categorycustom,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Purpletree\Marketplace\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productcollection,
        \Magento\Catalog\Model\Product\Action $actionStatus,
        Filesystem $filesystem,
        AttributeSetFactory $attributeSetFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Eav\Model\AttributeSetManagement $attributeSetManagement,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper
    ) {
        $this->customerModel       =       $customerModel;
        $this->indexerRegistry       =       $indexerRegistry;
        $this->messageManager = $messageManager;
        $this->_request = $request;
          $this->dataHelper               =       $dataHelper;
        $this->categorycustom = $categorycustom;
        $this->_categoryFactory = $categoryFactory;
            $this->actionStatus = $actionStatus;
            $this->productcollection  = $productcollection ;
        $this->_sellerFactory = $sellerFactory;
                $this->attributeSetFactory   =      $attributeSetFactory;
        $this->sellercustom = $sellercustom;
        $this->_mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->customerFactory = $customerFactory;
        $this->customerfactorysave = $customerfactorysave;
        $this->scopeConfig           =      $scopeConfig;
        $this->_fileUploaderFactory = $fileUploaderFactory;
          $this->_eavTypeFactory       =      $eavTypeFactory;
          $this->attributeSetManagement          =      $attributeSetManagement;
    }
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $customerId = $observer->getEvent()->getCustomer()->getId();
            $target = $this->_mediaDirectory->getAbsolutePath('marketplace/');
            $data = $this->_request->getPostValue('seller');
            if ($data) {
                if (isset($data['store_url'])) {
                     $samurl = $this->sellercustom->checkUniqueUrlcustomer($data['store_url']);
                    if ($samurl != '' && $samurl != $customerId) {
                        $this->messageManager
                        ->addError(__('Store Url already exits. Choose different store url.'));
                        throw new \Exception();
                    }
                }
                if (isset($data['is_seller']) && $data['is_seller'] == 1) {
                    $this->saveattributeValue($data['is_seller'], $customerId);
                    $this->messageManager->addSuccess(__('Seller has been created.'));
                }
                if (isset($data['remove_seller']) && $data['remove_seller'] == 1) {
                    if ($data['remove_seller'] == 1) {
                        $this->saveattributeValue(0, $customerId);
                        $this->messageManager->addSuccess(__('Seller has been removed.'));
                    }
                }
                
                if (isset($data['store_name']) && $data['store_name'] != '') {
                    $sellerData=$this->sellercustom
                    ->getStoreDetails($customerId);
                    if (isset($data['entity_idpts']) && $data['entity_idpts'] != '') {
                        $sellerdata = $this->_initSeller($customerId, $data['entity_idpts'], $data['store_url']);
                        if (isset($data['remove_seller']) && $data['remove_seller'] == 1) {
                            if ($data['remove_seller'] == 1) {
                                $data['status_id'] = 0;
                            }
                        }
                    } else {
                       // $sellerdata = $this->_initSeller($customerId, '', '');
					    $sellerdata    = $this->_sellerFactory->create();
						if ($this->sellercustom->getsellerEntityId($customerId) != '') {
								$id = $this->sellercustom->getsellerEntityId($customerId);
								$sellerdata->load($id);
							} else {
								$sellerdata->setStoreName($data['store_name']);
								$sellerdata->setStoreUrl($data['store_url']);
								$sellerdata->setSellerId($customerId);
								$sellerdata->save();
							}
                        $data['seller_id'] = $customerId;
                        //
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
                        //
                    }
                    $resul2 = $sellerdata->getStoreLogo();
                    $resul1 = $sellerdata->getStoreBanner();
                    $sellerdata->setData($data);
                    $sellerdata->save();
                    if (isset($data['store_logo']) && $data['store_logo'] !='') {
                        try {
                            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                            $uploader = $this->_fileUploaderFactory->create(
                                ['fileId' => 'seller[store_logo]']
                            );
                            $uploader->setAllowedExtensions(
                                ['jpg', 'jpeg', 'gif', 'png']
                            );
                            $uploader->setAllowRenameFiles(true);
                            $uploader->setFilesDispersion(true);
                            $uploader->setAllowCreateFolders(true);
                            $resul2 = $uploader->save($target);
                            $resul2 = 'marketplace/'.$resul2['file'];
                        } catch (\Exception $e) {
                            $this->messageManager
                            ->addError(__('Somthing went wrong while saving Store Logo.'));
                        }
                    }
                    $data['store_logo'] = $resul2;
                    if (isset($data['store_banner']) && $data['store_banner'] !='') {
                        try {
                            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                            $uploader = $this->_fileUploaderFactory->create(
                                ['fileId' => 'seller[store_banner]']
                            );
                            $uploader->setAllowedExtensions(
                                ['jpg', 'jpeg', 'gif', 'png']
                            );
                            $uploader->setAllowRenameFiles(true);
                             $uploader->setFilesDispersion(true);
                            $uploader->setAllowCreateFolders(true);
                            $resul1 = $uploader->save($target);
                            $resul1 = 'marketplace/'.$resul1['file'];
                        } catch (\Exception $e) {
                            $this->messageManager
                            ->addError(__('Somthing went wrong while saving Store Banner.'));
                        }
                    }
                        
                    $data['store_banner'] = $resul1;
                        
                    $sellerdata->setStoreLogo($data['store_logo']);
                        
                    $sellerdata->setStoreBanner($data['store_banner']);
					if(isset($data['store_commission'])) {
                    if ($data['store_commission'] == '') {
                        $sellerdata->setStoreCommission(null);
                    } else {
                        $sellerdata->setStoreCommission($data['store_commission']);
                    }
					}
                    $sellerdata->save();
                    if (isset($data['status_id']) && $sellerData['status_id']==0) {
                        $prodids = [];
                        $productcollectioddn = $this->productcollection
                                              ->addAttributeToSelect('entity_id')
                                              ->addAttributeToFilter('seller_id', $customerId);
                        foreach ($productcollectioddn as $proo) {
                            $prodids[] = $proo->getId();
                        }
                        $attrData = ['status' => 2];
                        if (!empty($prodids)) {
                            $this->actionStatus->updateAttributes($prodids, $attrData, 0);
                        }
                        $customerObj = $this->customerModel->load($customerId);
                        $message='Thanks for registration as seller.';
                        try {
                            $this->mailToSeller($message, $data['store_url'], $customerObj);
                            $this->mailToAdmin($message, $data['store_url'], $customerObj);
                        } catch (\Magento\Framework\Exception\LocalizedException $e) {
                            $messagecatch = $e->getMessage();
                        } catch (\RuntimeException $e) {
                            $messagecatch = $e->getMessage();
                        } catch (\Exception $e) {
                            $messagecatch = 'Something went wrong.';
                        }
                    }
                }
                 
                if (isset($data['category'])) {
                    if (!empty($data['category'])) {
                        $this->categorycustom->deleteSellerCategories($customerId);
                        $categorydata = $this->_categoryFactory->create();
                        foreach ($data['category'] as $catego) {
                            if ($catego != 0) {
                                $datacat['seller_id'] = $customerId;
                                $datacat['category_id'] = $catego;
                                $categorydata->setData($datacat);
                                $categorydata->save();
                            }
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager
            ->addException($e, __('Something went wrong..'));
        }
    }
    private function reIndexCustomer($customerId)
    {
        $indexerIds = ['customer_grid'];
            $startTime = microtime(true);
        foreach ($indexerIds as $indexerId) {
            try {
                $indexer = $this->indexerRegistry->get($indexerId);
                ;
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
        /** @var \Magento\Customer\Model\ResourceModel\CustomerFactory $customerResourceFactory */
        $customerResource = $this->customerfactorysave->create();
        $customerResource->saveAttribute($customer, 'is_seller');
        if ($value == 0) {
            $prodids = [];
                            $productcollectioddn = $this->productcollection
                                                  ->addAttributeToSelect('entity_id')
                                                  ->addAttributeToFilter('seller_id', $customerId);
            foreach ($productcollectioddn as $proo) {
                $prodids[] = $proo->getId();
            }
                            $attrData = ['status' => 2];
            if (!empty($prodids)) {
                $this->actionStatus->updateAttributes($prodids, $attrData, 0);
            }
        }
         $this->reIndexCustomer($customerId);
    }
     /**
      * Init Seller
      *
      * @return \Purpletree\Marketplace\Model\Post
      */
    protected function _initSeller($customerId, $id = null, $store_url = null)
    {
        /** @var \Purpletree\Marketplace\Model\Post $post */
        $seller    = $this->_sellerFactory->create();
        if ($id != null) {
            $seller->load($id);
        } elseif ($this->sellercustom->getsellerEntityId($customerId) != '') {
            $id = $this->sellercustom->getsellerEntityId($customerId);
            $seller->load($id);
        }
        return $seller;
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
