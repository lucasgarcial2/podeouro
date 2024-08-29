<?php
/**
 * Purpletree_Marketplace Brandsave
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */
 
namespace Purpletree\Marketplace\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Customer\Model\Session as CustomerSession;

class Brandsave extends Action
{

    /**
     * Constructor
     *
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     * @param \Magento\Eav\Setup\EavSetupFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Magento\Framework\App\Action\Context
     *
     */

    public $customer;
    public $_eavSetupFactory;
    public $storeManager;
    public $_attributeFactory;
    public $_attributeFactory1;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;


    public function __construct(
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attributeFactory,
        \Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactoryf,
        \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {

        $this->customer                 = $customer;
        $this->_eavSetupFactory     = $eavSetupFactory;
        $this->storeManager             = $storeManager;
        $this->_attributeFactory    = $attributeFactory;
        $this->_attributeFactory1    = $attributeFactoryf;
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
        $seller_id = $this->customer->getId();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            try {
                $this->saveattribute($data, $seller_id);
                 $this->messageManager->addSuccess(__('You saved the  Brand.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Brand.--------->'.$e->getMessage()));
                if (isset($data['attribute_id'])) {
                    return $this->_redirect('marketplace/index/editbrandmanagement', ['id' =>$data['attribute_id']]);
                }
            }
        }
                return $this->_redirect('marketplace/index/brandmanagement');
    }
    
    /**
     * Save Attribute
     *
     */
    public function saveattribute($data, $seller_id)
    {
	/* 	echo "<pre>";
		print_r($data);
		die; */
        $optionId = $this->getRequest()->getParam('option_id');
        //$storeId = $this->getRequest()->getParam('store', 0);
        $storeId = $this->storeManager->getStore()->getId();;
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $attricode   = $objectManager->create('\Amasty\ShopbyBase\Model\AllowedRoute');
		$filterCode = $attricode->getBrandCode();
                /** @var \Amasty\ShopbyBase\Model\OptionSetting $model */
                $model = $this->_objectManager->create(\Amasty\ShopbyBase\Model\OptionSetting::class);
                $data = $this->filterData($data);
                $model->saveData($filterCode, $optionId, $storeId, $data);
    }
	 /**
     * @param $data
     *
     * @return mixed
     */
    protected function filterData($data)
    {
        $inputFilter = new \Zend_Filter_Input(
            [],
            [],
            $data
        );
        $data = $inputFilter->getUnescaped();

        return $data;
    }
}
