<?php
/**
 * Purpletree_Marketplace Attributes
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2020
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Block;

class Attributes extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context,
     * @param \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param array $data
     */
    public $attributeRepository;
    public $coreRegistry;
    
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    
    /**
     * Attribute Code
     *
     * @return Attribute Code
     */
    public function getCode($attribute_code)
    {
        $customer_id = $this->coreRegistry->registry('current_customer_id');
        $exploded_data = explode("_seller_", $attribute_code);
		 if (isset($exploded_data[1])) {
            if ($this->sellerid() == end($exploded_data)) {
				array_pop($exploded_data);
				return implode('_seller_',$exploded_data); 
            }
        }  else {
			return $attribute_code;
		}
		//array_pop($exploded_data);
		//return implode('_seller_',$exploded_data); 
    }

    /**
     * Seller ID
     *
     * @return Seller ID
     */
    public function sellerid()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }

    /**
     * Validate Seller
     *
     * @return Validate Seller
     */
    public function validateSeller($attribute_code)
    {
        $exploded_data = explode("_seller_", $attribute_code);
        if (isset($exploded_data[1])) {
            if ($this->sellerid() == end($exploded_data)) {
				array_pop($exploded_data);
				return implode('_seller_',$exploded_data); 
            }
        }  else {
			if($attribute_code != 'seller_id') {
			return $attribute_code;
			}
		}
    }
    /**
     * Validate Seller
     *
     * @return Validate Seller
     */
    public function isAdminattribute($attribute_code)
    {
        $exploded_data = explode("_seller_", $attribute_code);
        if (isset($exploded_data[1])) {
            if ($this->sellerid() == end($exploded_data)) {
				return 0; 
            }
        }  else {
			return 1;
		}
    }

    /**
     * Get All Attributes
     *
     * @return All Attributes
     */
    public function getAllAttributes()
    {
        $attributes = [];
          $attributeRepository = $this->attributeRepository->getAllAttributes();
        foreach ($attributeRepository->getItems() as $attribute) {
            if ($this->validateSeller($attribute->getAttributeCode()) != '') {
                $attributes[] = [
                'id' => $attribute->getId(),
                'code' => $attribute->getAttributeCode(),
                'isadmin' => $this->isAdminattribute($attribute->getAttributeCode()),
                'label' => $attribute->getStoreLabel($this->_storeManager->getStore()->getID()),
                'IsRequired' => (($attribute->getIsRequired() == 1)? 'Yes' : 'No'),
                'IsSystemDefined' => (($attribute->getIsUserDefined() == 1)? 'No' : 'Yes')
                ];
            }
        }
      
        return $attributes;
    }
}
