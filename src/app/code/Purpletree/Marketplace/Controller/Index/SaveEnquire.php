<?php
/**
 * Purpletree_Marketplace SaveEnquire
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

class SaveEnquire extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\MediaStorage\Model\File\UploaderFactory
     * @param \Purpletree\Marketplace\Model\Upload
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $customerModel;
    public $customer;
    public $vendorContact;
    public $resultForwardFactory;
    public $dataHelper;
    public $seller;
    public $scopeConfig;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Magento\Customer\Model\Customer $customerModel,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\VendorContact $vendorContact,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $seller,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        $this->customerModel             =       $customerModel;
        $this->customer             =       $customer;
        $this->vendorContact        =       $vendorContact;
        $this->resultForwardFactory =       $resultForwardFactory;
        $this->dataHelper           =       $dataHelper;
        $this->seller               =       $seller;
        $this->scopeConfig          =       $scopeConfig;
        parent::__construct($context);
    }
    
    public function execute()
    {
        
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        
        $data = $this->getRequest()->getPostValue();

        if ($moduleEnable) {
            if ($data) {
                try {
                    $sellerInfo=$this->seller->getStoreDetails($data['seller_id']);
                    $this->vendorContact->setData($data);
                    $this->vendorContact->save();
                    try {
                        $this->mailToSeller($data);
                    } catch (\Magento\Framework\Exception\LocalizedException $e) {
                        $messagecatch = $e->getMessage();
                    } catch (\RuntimeException $e) {
                        $messagecatch = $e->getMessage();
                    } catch (\Exception $e) {
                        $messagecatch = 'Something went wrong.';
                    }
                    $this->messageManager
                    ->addSuccess(__('Enquire submitted successfully to the seller'));
                    return $this->_redirect($sellerInfo['store_url']);
                } catch (\Exception $e) {
                    return $this->_redirect($sellerInfo['store_url']);
                    $this->messageManager
                    ->addException($e, __('Something went wrong while saving the details'.$e->getMessage()));
                }
            }
        } else {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
    }
    
    /**
     *   Vendor Email to Seller
     *
     *
     */
    public function mailToSeller($data)
    {
        $identifier    = 'vendor_contact';
        try {
            $customerObj = $this->customerModel->load($data['seller_id']);
            $emailTemplateVariables = [];
            $emailTemplateVariables['customer_enquire'] = $data['customer_enquire'];
            $emailTemplateVariables['customer_email'] =$data['customer_email'];
            $emailTemplateVariables['customer_name'] =$data['customer_name'];
            $emailTemplateVariables['customer_referral_url'] =$data['customer_referral_url'];
            $emailTemplateVariables['seller_name'] =$customerObj->getName();
            $error = false;
            $sender = [
            'name' => $this->getStoreName(),
            'email' =>$this->getStoreEmail()
            ];
            $receiver = [
            'name' =>$customerObj->getName(),
            'email' => $customerObj->getEmail()
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
    public function getStoreEmail()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    public function getStoreName()
    {
        return $this->scopeConfig
        ->getValue('trans_email/ident_general/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
