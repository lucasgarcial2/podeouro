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
use Magento\Framework\Setup\ModuleDataSetupInterface;
use \Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Validator\Regex;

class AttributeSave extends Action
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
                 $this->messageManager->addSuccess(__('You saved the product attribute.'));
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Attribute.--------->'.$e->getMessage()));
                if (isset($data['attribute_id'])) {
                    return $this->_redirect('marketplace/index/editattribute', ['id' =>$data['attribute_id']]);
                }
                return $this->_redirect('marketplace/index/newattribute');
            }
        }
                return $this->_redirect('marketplace/index/attributes');
    }
    
    /**
     * Save Attribute
     *
     */
    public function saveattribute($data, $seller_id)
    {
		/* echo "<pre>";
		print_r($data);
		die;  */
		    if (!isset($data['attribute_id'])) {
        $attributeCode = $data['attribute_code'].'_seller_'.$seller_id;
			} else {
        $attributeCode = $data['attribute_code'];
			}
        if (strlen($data['attribute_code']) > 19) {
            $this->messageManager->addError(
                __(
                    'Attribute code "%1" is invalid. Please enter less or equal than 19 symbols..',
                    $data['attribute_code']
                )
            );
                 return $this->_redirect('marketplace/index/newattribute');
        }
        if (strlen($attributeCode) > 0) {
            $validatorAttrCode = new Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
            if (!$validatorAttrCode->isValid($attributeCode)) {
                $this->messageManager->addError(
                    __(
                        'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                        'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                        $attributeCode
                    )
                );
                return $this->_redirect('marketplace/index/newattribute');
            }
        }
            $validatorAttrCode = new Regex(['pattern' => '/^[a-z][a-z_0-9]{0,30}$/']);
        if (!$validatorAttrCode->isValid($attributeCode)) {
            $this->messageManager->addError(
                __(
                    'Attribute code "%1" is invalid. Please use only letters (a-z), ' .
                    'numbers (0-9) or underscore(_) in this field, first character should be a letter.',
                    $attributeCode
                )
            );
            return $this->_redirect('marketplace/index/newattribute');
        }
        
        $eavSetup = $this->_eavSetupFactory->create();
        if (!isset($data['attribute_id'])) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                $attributeCode, /* Custom Attribute Code */
                [
                'type' => 'int',/* Data type in which formate your value save in database*/
                'backend' => '',
                'frontend' => '',
                'label' => $data['attribute_label'], /* lablel of your attribute*/
                'input' => 'select',
                'class' => '',
                'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                                /* Source of your select type custom attribute options*/
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                                    /*Scope of your attribute */
                'visible' => true,
                'required' => $data['is_required'],
                'user_defined' => true,
                'default' => '',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
                ]
            );
        } else {
                  $model = $this->_attributeFactory1->create();
                  $model->load($data['attribute_id']);
                  $model->setDefaultFrontendLabel($data['attribute_label']);
                  $model->setIsRequired($data['is_required']);
                  $model->save();
        }
        
        $attributeInfo=$this->_attributeFactory->getCollection()
           ->addFieldToFilter('attribute_code', ['eq'=>$attributeCode])
           ->getFirstItem();
          $attribute_id = $attributeInfo->getAttributeId();
        //$labeddls[0] = $data['attribute_label'];
       // $labeddls[1] = $data['attribute_label'];
        //$attributeInfo->setStoreLabels($labeddls)->save();
        
        $option = [];
        $options = $attributeInfo->getSource()->getAllOptions();
            
        if (!empty($options)) {
            foreach ($options as $opt) {
                if (isset($data['optionsoldall'])) {
                    if (!in_array($opt['value'], $data['optionsoldall'])) {
                        $option['value'][$opt['value']] = true;
                        $option['delete'][$opt['value']] = true;
                    }
                } else {
                        $option['value'][$opt['value']] = true;
                        $option['delete'][$opt['value']] = true;
                }
            }
            $eavSetup->addAttributeOption($option);
        }
        $option = [];
     
        if (!empty($data['options'])) {
            $option['attribute_id'] = $attributeInfo->getAttributeId();
        
            foreach ($data['options'] as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $kkey1 => $val) {
                        if (isset(explode('_', $kkey1)[1])) {
                            $option['value'][$val[0]][0]= $val[0];
                        } else {
                            $option['value'][$kkey1][0]= $val[0];
                        }
                    }
                } else {
                      $option['value'][$value][0]= $value;
                }
            }
        
            $eavSetup->addAttributeOption($option);
        }
        
        if (isset($data['attributeset'])) {
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);//4
            $autosettingsTabName = 'Seller Configurable Attribute';
            $eavSetup->addAttributeGroup($entityTypeId, $data['attributeset'], $autosettingsTabName, 60);
            $groupId = $eavSetup->getAttributeGroupId($entityTypeId, $data['attributeset'], "Seller Configurable Attribute");
            $eavSetup->addAttributeToSet($entityTypeId, $data['attributeset'], $groupId, $attribute_id);
        }
    }
}
