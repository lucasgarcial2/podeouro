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
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Customer\Model\Session as CustomerSession;

class DeleteAttribute extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Purpletree\Marketplace\Model\AttributesList
     * @param \Magento\Framework\Json\Helper\Data
     * @param \Magento\Framework\View\Result\PageFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     *
     */

    public $customer;
    public $storeManager;
    public $_attributeFactory;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;

    public function __construct(
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->customer                 = $customer;
        $this->storeManager             = $storeManager;
        $this->_attributeFactory = $attributeFactory;
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
                $model = $this->_attributeFactory;
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the product attribute.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while deleting the Attribute.--------->'.$e->getMessage()));
            }
        } else {
             $this->messageManager->addError(__('Delete Attribute Id not found'));
        }
            return $this->_redirect('marketplace/index/attributes');
    }
}
