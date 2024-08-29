<?php
/**
 * Purpletree_Marketplace Save
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

class saveshippingrate extends Action
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
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\App\Action\Context
     *
     */
    public $_resultForwardFactory;
    public $storeManager;
    public $storeDetails;
    public $dataHelper;
    public $customer;
    public $tablerate;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Purpletree\Marketplace\Model\TablerateFactory $tablerate,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        Context $context
    ) {
		$this->_resultForwardFactory = $resultForwardFactory;
        $this->storeManager = $storeManager;
        $this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
        $this->customer = $customer;
        $this->tablerate     = $tablerate;
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
        $data = $this->getRequest()->getPostValue();
		
        if ($data) {
            try {
				$website = $this->storeManager->getWebsite();
				$data['condition_name'] = $website->getConfig('carriers/purpletreetablerate/condition_name');
				$data['website_id'] = $website->getId();
				$data['seller_id'] = $customerId;
				$data['dest_zip'] = (string)trim($data['dest_zip']);
				if($data['dest_zip'] == '') {
					$data['dest_zip'] = '*';
				}
				   $price = $this->_parseDecimalValue($data['price']);
				   $data['price'] = $price;
				   $condition_value = $this->_parseDecimalValue($data['condition_value']);
				   $data['condition_value'] = $condition_value;
        if ($price !== false && $condition_value !== false) {
			  $sellersave    = $this->tablerate->create();
				if(isset($data['pk'])) {
						$sellersave->load($data['pk']);
						if($sellersave->getSellerId() != $data['seller_id']) {
							 $this->messageManager->addError(__('You can not edit this shipping Rate'));
							  return $this->_redirect('marketplace/index/sellershipping');
						}
				}
				
              $sellersave->setData($data);
              $sellersave->save();
                  $this->messageManager->addSuccess(__('Shipping Rate has been saved.'));
        } else {
			if ($price === false) {
              $this->messageManager->addError(__('The shipping price is incorrect. Verify the shipping price and try again.'));
			} elseif($condition_value === false) {
              $this->messageManager->addError(__('The condition value is incorrect. Verify and try again.'));
			}
			if(isset($data['pk'])) {
				  return $this->_redirect('marketplace/index/editshipping', ['id' =>$data['pk']]);
			} else {
                    return $this->_redirect('marketplace/index/newshipping');
			} 
		}
            } catch (\Exception $e) {
				
                $this->messageManager->addException($e, __('Something went wrong while saving the Shipping Rate. '.$e->getMessage()));
            }
        }
                    return $this->_redirect('marketplace/index/sellershipping');
    }

	  /**
     * Parse and validate positive decimal value
     *
     * Return false if value is not decimal or is not positive
     *
     * @param string $value
     * @return bool|float
     */
    private function _parseDecimalValue($value)
    {
        $result = false;
        if (is_numeric($value)) {
            $value = (double)sprintf('%.4F', $value);
            if ($value >= 0.0000) {
                $result = $value;
            }
        }
        return $result;
    }
}
