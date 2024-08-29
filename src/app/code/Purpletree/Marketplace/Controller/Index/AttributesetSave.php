<?php
/**
 * Purpletree_Marketplace AttributeSave
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
use Magento\Eav\Model\Entity\Attribute\SetFactory as AttributeSetFactory;
use \Magento\Customer\Model\Session as CustomerSession;

class AttributesetSave extends Action
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
    public $attributeSetManagement;
    public $attributeSet;
    public $customer;
    public $storeManager;
    public $_eavTypeFactory;
     public $attributeSetFactory;
    public $storeDetails;
    public $dataHelper;
    public $resultForwardFactory;
    public $_resultPage;


    public function __construct(
        CustomerSession $customer,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AttributeSetFactory $attributeSetFactory,
        \Magento\Eav\Model\Entity\Attribute\Set $attributeSet,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Eav\Model\AttributeSetManagement $attributeSetManagement,
        \Magento\Eav\Model\Entity\TypeFactory $eavTypeFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Context $context
    ) {
        $this->attributeSetManagement                 = $attributeSetManagement;
        $this->attributeSet                 = $attributeSet;
        $this->customer                 = $customer;
        $this->storeManager             = $storeManager;
        $this->_eavTypeFactory = $eavTypeFactory;
         $this->attributeSetFactory = $attributeSetFactory;
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
            $entityTypeCode = 'catalog_product';
            $entityType     = $this->_eavTypeFactory->create()->loadByCode($entityTypeCode);
            $defaultSetId   = (isset($data['defaultSetId'])? $data['defaultSetId']:0);
            $datas = [
                        'attribute_set_name'    => $data['attribute_set_name']."_seller_".$seller_id,
                        'entity_type_id'        => $entityType->getId()
                    ];
                    $ff = $data['attribute_set_name'];
                    $rrr = $ff.'_seller_'.$seller_id;
            try {
                if (isset($data['attributeid'])) {
                 /* @var $model \Magento\Eav\Model\Entity\Attribute\Set */
                    $model = $this->attributeSet->setEntityTypeId($entityType->getId());
                    $model->load($data['attributeid']);
                    $model->setAttributeSetName(trim($data['attribute_set_name']."_seller_".$seller_id));
                    $model->validate();
                    $model->save();
                } else {
                     $attributeSet = $this->attributeSetFactory->create();
                     $attributeSet->setData($datas);
                     $this->attributeSetManagement->create($entityTypeCode, $attributeSet, $defaultSetId);
                }
                 $this->messageManager->addSuccess(__('You saved the attribute set.'));
            } catch (\Exception $e) {
                if ($e->getMessage() == 'An attribute set named "'.$rrr.'" already exists.') {
                    $this->messageManager->addException($e, __('An attribute set named "'.$ff.'" already exists.'));
                } else {
                    $this->messageManager->addException($e, __('Something went wrong while saving the Attribute Set'.$e->getMessage()));
                }
            }
        }
                return $this->_redirect('marketplace/index/attributeset');
    }
}
