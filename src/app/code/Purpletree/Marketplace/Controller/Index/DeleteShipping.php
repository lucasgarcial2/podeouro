<?php
/**
 * Purpletree_Marketplace DeleteAttribute
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
use \Magento\Customer\Model\Session as CustomerSession;

class DeleteShipping extends Action
{
   public $customer;
   public $storeManager;
   public $tablerate;
   public $storeDetails;
   public $dataHelper;
   public $resultForwardFactory;
   public $_resultPage;
   
    public function __construct(
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
		   \Purpletree\Marketplace\Model\TablerateFactory $tablerate,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->customer                 = $customer;
        $this->storeManager             = $storeManager;
		$this->tablerate     = $tablerate;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
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
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->tablerate->create();
                $model->load($id);
				if($model->getSellerId() == $customerId) {
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the Shipping Rate.'));
				} else {
                $this->messageManager->addError(__('You can not delete this Shipping Rate.'));
				}
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting the Shipping Rate.--------->'.$e->getMessage()));
            }
        } else {
             $this->messageManager->addError(__('Delete Shipping Rate Id not found'));
        }
            return $this->_redirect('marketplace/index/sellershipping');
    }
}
