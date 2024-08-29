<?php
/**
 * Purpletree_Marketplace RemoveSeller
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
use \Magento\Customer\Model\Session as CustomerSession;

class RemoveSeller extends Action
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
     * @param \Magento\Framework\App\Action\Context
     *
     */

    public $indexerRegistry;
    public $customer;
    public $customerFactory;
    public $dataHelper;
    public $actionStatus;
    public $productcollection;
    public $storeDetails;
    public $customerfactorysave;
    public $resultForwardFactory;
    public $sellerFactory;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerFactory $customerfactorysave,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productcollection,
        \Magento\Catalog\Model\Product\Action $actionStatus,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        Context $context
    ) {
        $this->indexerRegistry       =       $indexerRegistry;
        $this->customer              =      $customer;
        $this->customerFactory       =      $customerFactory;
        $this->dataHelper            =      $dataHelper;
         $this->actionStatus = $actionStatus;
            $this->productcollection  = $productcollection ;
        $this->storeDetails          =      $storeDetails;
        $this->customerfactorysave   =      $customerfactorysave;
        $this->resultForwardFactory  =      $resultForwardFactory;
        $this->sellerFactory         =      $sellerFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        $data = $this->getRequest()->getPostValue();
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        if ($data) {
            try {
                $storeid=$this->storeDetails->storeId($seller);
                $sellersave    = $this->sellerFactory->create();
                $sellersave->load($storeid);
                if ($data['remvseller']==1) {
                    $sellersave->setStatusId(0);
                    $this->saveattributeValue(0, $seller);
                    $this->messageManager->addSuccess(__('Successfully remove as seller'));
                    $sellersave->save();
                }
                return $this->_redirect('customer/account/index');
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
                ;
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
        if ($value == 0) {
            $prodids = [];
                            $productcollectioddn = $this->productcollection
                                                  ->addAttributeToSelect('entity_id')
                                                  ->addAttributeToFilter('seller_id', $customerId);
            if (!empty($productcollectioddn)) {
                foreach ($productcollectioddn as $proo) {
                    $prodids[] = $proo->getId();
                }
            }
            if (!empty($prodids)) {
                            $attrData = ['status' => 2];
                        $this->actionStatus->updateAttributes($prodids, $attrData, 0);
            }
        }
        $this->reIndexCustomer($customerId);
    }
}
