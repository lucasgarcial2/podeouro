<?php
/**
 * Purpletree_Marketplace StoreSave
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

class StoreSave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Purpletree\Marketplace\Model\Upload
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Purpletree\Marketplace\Model\SellerFactory
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $customer;
    public $sellerFactory;
    public $uploadModel;
    public $storeManager;
    public $coreRegistry;
    public $storeDetails;
    public $resultForwardFactory;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Purpletree\Marketplace\Model\Upload $uploadModel,
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Model\SellerFactory $sellerFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->customer             =       $customer;
        $this->sellerFactory        =       $sellerFactory;
        $this->uploadModel          =       $uploadModel;
        $this->storeManager         =       $storeManager;
        $this->coreRegistry         =       $coreRegistry;
        $this->storeDetails             =       $storeDetails;
        $this->resultForwardFactory =       $resultForwardFactory;
        $this->dataHelper           =       $dataHelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isavialableSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
            $this->customer->setAfterAuthUrl($this->storeManager->getStore()->getCurrentUrl());
            $this->customer->authenticate();
        }
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $storeid=$this->storeDetails->storeId($seller);
                $sellersave    = $this->sellerFactory->create();
                $sellersave->load($storeid);
                $banner = $this->uploadModel->uploadFileAndGetName('banner', $data, $data['store_old_banner']);
                $logo = $this->uploadModel->uploadFileAndGetName('logo', $data, $data['store_old_logo']);
                $sellersave->setStoreName($data['store_name']);
                $sellersave->setStorePhone($data['store_phone']);
                if ($banner!='') {
                    $sellersave->setStoreBanner('marketplace'.$banner);
                } else {
                    $sellersave->setStoreBanner($data['store_old_banner']);
                }
                if ($logo!='') {
                    $sellersave->setStoreLogo('marketplace'.$logo);
                } else {
                    $sellersave->setStoreLogo($data['store_old_logo']);
                }
                    $sellersave->setStoreDescription($data['store_description']);
                    $sellersave->setStoreShippingPolicy($data['store_shipping_policy']);
                    $sellersave->setStoreReturnPolicy($data['store_return_policy']);
                    $sellersave->setStoreMetaKeywords($data['store_meta_keywords']);
                    $sellersave->setStoreMetaDescriptions($data['store_meta_descriptions']);
                    $sellersave->setStoreAddress($data['store_address']);
                    $sellersave->setStoreCity($data['store_city']);
                if (is_numeric($data['store_region'])) {
                    $sellersave->setStoreRegionId($data['store_region']);
                    $sellersave->setStoreRegion('');
                } else {
                    $sellersave->setStoreRegion($data['store_region']);
                    $sellersave->setStoreRegionId(0);
                }
                    $sellersave->setStoreCountry($data['store_country']);
                    $sellersave->setStoreZipcode($data['store_zipcode']);
                    $sellersave->setStoreTinNumber($data['store_tin_number']);
                    $sellersave->setStoreBankAccount($data['store_bank_account']);
                if(isset($data['stripe_account_id'])) {
                    $sellersave->setStripeAccountId($data['stripe_account_id']);
                }
                    $sellersave->save();
                    $this->messageManager->addSuccess(__('Store updated successfully'));
                    return $this->_redirect('marketplace/index/sellers');
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the details'));
            }
        }
        return $this->_redirect('marketplace/index/sellers');
    }
}
