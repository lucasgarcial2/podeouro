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

class ExportShipping extends Action
{

    public $_storeManager;
    public $_resultForwardFactory;
    public $_fileFactory;
    public $coreRegistry;
    public $storeDetails;
    public $dataHelper;
    public $customer;
    public $_resultPage;

    public function __construct(
		CustomerSession $customer,
       \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
	   \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
		 \Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
       	$this->_storeManager = $storeManager;
		$this->_resultForwardFactory = $resultForwardFactory;
        $this->_fileFactory = $fileFactory;
		   $this->coreRegistry = $coreRegistry;
		$this->storeDetails             =       $storeDetails;
        $this->dataHelper           =       $dataHelper;
		  $this->customer = $customer;
        parent::__construct($context);
    }
    
    public function execute()
    {
		 $customerId=$this->customer->getCustomer()->getId();
        $seller=$this->storeDetails->isSeller($customerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$this->customer->isLoggedIn()) {
                $this->customer->setAfterAuthUrl($this->_storeManager->getStore()->getCurrentUrl());
                $this->customer->authenticate();
        }
        if ($seller=='' || !$moduleEnable) {
            $resultForward = $this->_resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
		       $fileName = 'ptstablerates.csv';
        /** @var $gridBlock \Purpletree\Marketplace\Block\Adminhtml\Carrier\Tablerate\Grid */
		 $this->coreRegistry->register('current_customer_id', $this->customer->getId());
        $gridBlock = $this->_view->getLayout()->createBlock(
            \Purpletree\Marketplace\Block\Carrier\Tablerate\Grid::class
        ); 
        $website = $this->_storeManager->getWebsite();
        $conditionName = $website->getConfig('carriers/purpletreetablerate/condition_name');
        $gridBlock->setWebsiteId($website->getId())->setConditionName($conditionName);
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
	}
}