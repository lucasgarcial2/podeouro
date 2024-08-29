<?php
/**
 * Purpletree_Marketplace Editbrandmanagement
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

class Editbrandmanagement extends \Magento\Framework\View\Element\Template
{
     /**
      * Constructor
      *
      * @param \Magento\Framework\View\Element\Template\Context
      * @param \Magento\Framework\Registry
      * @param array $data
      */

      public $coreRegistry;
      public $eavConfig;
      public $attributeFactory;
      public $_blockFactory;
      public $_productMetadataInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
		  \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
		 \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->coreRegistry         = $coreRegistry;
		 $this->eavConfig = $eavConfig;
		$this->attributeFactory = $attributeFactory;
		$this->_blockFactory = $blockFactory;
		   $this->_productMetadataInterface        = $productMetadataInterface;
        parent::__construct($context, $data);
    }
    public function getVersion()
    {
        return $this->_productMetadataInterface->getVersion();
    }
    
    /**
     * Attribute
     *
     * @return Attribute
     */
    public function getAttribute()
    {
         return $this->coreRegistry->registry('current_brand');
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
     * @return array
     */
    public function allCMSBlocks()
    {
        $ret = [];
        $dd = $this->_blockFactory->create()->getCollection();
		if($dd) {
        foreach ($dd as $hhh) {
            if ($hhh->getIsActive()) {
                 $ret[] = [
                    'value' => $hhh->getId(),
                    'label' => $hhh->getTitle()
                    ];
            }
        }
		}
        return $ret;
    }
	public function getBrandName($attribute_id) {
		 $attribute = $this->attributeFactory->create();
		 $objectManager 		     = \Magento\Framework\App\ObjectManager::getInstance();
        $attricode   = $objectManager->create('\Amasty\ShopbyBase\Model\AllowedRoute');
		$brand = $attricode->getBrandCode();
		$attribute11 = $this->eavConfig->getAttribute('catalog_product', $brand);
         $attribute->load($attribute11->getAttributeId());
		 if(!empty($optionssss = $attribute->getSource()->getAllOptions(false))) {
		 foreach($optionssss as $key => $attributeoptions) {
			 if($attributeoptions['value'] == $attribute_id) {
				 return $attributeoptions['label'];
			 }
		 }
		 }
	}
	public function getImageUrl()
    {
		$destinationFolder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $destinationFolder;
    }
}
