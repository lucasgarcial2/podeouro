<?php
/**
 * Purpletree_Marketplace EditAttribute
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

class EditAttribute extends \Magento\Framework\View\Element\Template
{
     /**
      * Constructor
      *
      * @param \Magento\Framework\View\Element\Template\Context
      * @param \Magento\Framework\Registry
      * @param array $data
      */

      public $coreRegistry;
      public $attributeRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Product\AttributeSet\Options $attributeRepository,
        array $data = []
    ) {
        $this->coreRegistry         = $coreRegistry;
         $this->attributeRepository = $attributeRepository;
        parent::__construct($context, $data);
    }
      
    /**
     * Attribute Code
     *
     * @return Attribute Code
     */
    public function getAttriCode()
    {
        $customer_id = $this->coreRegistry->registry('current_customer_id');
        return $attribute_code = $this->getAttribute()->getAttributeCode();
        //$exploded_data = explode("_seller_", $attribute_code);
       // array_pop($exploded_data);
		//return implode('_seller_',$exploded_data); 
    }
    
    /**
     * Attribute
     *
     * @return Attribute
     */
    public function getAttribute()
    {
         return $this->coreRegistry->registry('current_attribute');
    }
    public function attributesetlist()
    {
        return $this->attributeRepository->toOptionArray();
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
}
