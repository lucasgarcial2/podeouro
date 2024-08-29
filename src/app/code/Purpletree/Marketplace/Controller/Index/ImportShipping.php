<?php
/**
 * Purpletree_Marketplace ImportShipping
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

class ImportShipping extends Action
{
    public $_storeManager;
	public $_resultForwardFactory;
	public $coreRegistry;
    public $storeDetails;
    public $dataHelper;
	public $customer;
	public $_tablerateFactory;
    public $_resultPage;

    public function __construct(
		CustomerSession $customer,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
	   \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
		 \Magento\Framework\Registry $coreRegistry,
		         \Purpletree\Marketplace\Model\ResourceModel\Carrier\TablerateFactory $tablerateFactory,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
       	$this->_storeManager = $storeManager;
		$this->_resultForwardFactory = $resultForwardFactory;
		   $this->coreRegistry = $coreRegistry;
		$this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
		  $this->customer = $customer;
		    $this->_tablerateFactory = $tablerateFactory;
        parent::__construct($context);
    }
    
     public function execute()
    {
		 $seller_id=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($seller_id);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
                $this->customer->setAfterAuthUrl($this->_storeManager->getStore()->getCurrentUrl());
                $this->customer->authenticate();
        }
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
		  try {
                 $tableRate = $this->_tablerateFactory->create();
				$tableRate->uploadAndImport($this,$seller_id);
                 $this->messageManager->addSuccess(__('Shipping Rates imported successfully.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while Import the Sheet.-->'.$e->getMessage()));
            }
                return $this->_redirect('marketplace/index/sellershipping');
      
	}
}