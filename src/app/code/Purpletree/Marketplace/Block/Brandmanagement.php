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

class Brandmanagement extends \Magento\Framework\View\Element\Template
{
	protected $brandcollection;
     
    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context,
     * @param \Magento\Framework\Registry $coreRegistry,
     * @param array $data
     */

     public $eavConfig;
     public $resource;
     public $moduleManager;
     public $attributeFactory;
     public $coreRegistry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\Registry $coreRegistry,
		\Magento\Framework\Module\Manager $moduleManager,
			\Magento\Framework\App\ResourceConnection $resource,
        array $data = []
    ) {
		 $this->eavConfig = $eavConfig;
		     $this->resourcees   			  = $resource;
		 		$this->moduleManager 					= $moduleManager;
		$this->attributeFactory = $attributeFactory;
        $this->coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }
    protected function _prepareLayout()
    {
		
        parent::_prepareLayout();
        if ($this->getAllBrands()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.products.pager'
            )->setCollection(
                $this->getAllBrands()
            );
            $this->setChild('pager', $pager);
            $this->getAllBrands();
        }
        return $this;
    }
        /**
     * Pager Html
     *
     * @return Pager Html
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
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
     * Get All Attributes
     *
     * @return All Attributes
     */
    public function getinbrandsarray()
    {
		  $newcoll = array();
		  if ($this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand')) {
		  $objectManager 		     = \Magento\Framework\App\ObjectManager::getInstance();
		 $this->optionSettingFactory   = $objectManager->create('\Amasty\ShopbyBase\Model\OptionSettingFactory');
		 $attributeRepository = $this->optionSettingFactory->create();
		  $collectionnn = $attributeRepository->getCollection();
		$second_table_name = $this->resourcees->getTableName('eav_attribute_option_value'); 
		$eav_attribute_option = $this->resourcees->getTableName('eav_attribute_option'); 
			$collectionnn->getSelect()->joinInner(array('amshopbybrand_option' => $eav_attribute_option),
                                               '(main_table.value = amshopbybrand_option.option_id)');						
			$collectionnn->getSelect()->join(array('option' => $second_table_name),
                                               '(main_table.value = option.option_id)');
		  if(!empty($collectionnn)) {
			  foreach($collectionnn as $brandd) {
				  if (!array_key_exists($brandd['value'],$newcoll)) {
						$newcoll[$brandd['value']] = $brandd['option_setting_id'];
					}
				  if($this->getstoreId() == $brandd['store_id']) {
						$newcoll[$brandd['value']] = $brandd['option_setting_id'];
					}
				
			  
			  }
		  }
		  }
		  $newcol = implode(',',$newcoll);
		  return $newcol;
		  
	}
	public function getAllBrands()
    {
		  if ($this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand') && $this->moduleManager->isOutputEnabled('Amasty_ShopbyBase')) {
			    if (!$this->brandcollection) {
		$objectManager 		     = \Magento\Framework\App\ObjectManager::getInstance();
		 $this->optionSettingFactory   = $objectManager->create('\Amasty\ShopbyBase\Model\OptionSettingFactory');
		 $OptionSettingInterface  = $objectManager->create('\Amasty\ShopbyBase\Api\Data\OptionSettingInterface');
          $attributeRepository = $this->optionSettingFactory->create();
		 $this->brandcollection = $attributeRepository->getCollection()->addFieldToFilter('option_setting_id', array('in' => $this->getinbrandsarray()));
		 $attrCode   = $this->_scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $filterCode = \Amasty\ShopbyBase\Helper\FilterSetting::ATTR_PREFIX . $attrCode;
        $this->brandcollection->addFieldToFilter($OptionSettingInterface::FILTER_CODE, $filterCode);
       //$this->brandcollection->addFieldToFilter('main_table.' . $OptionSettingInterface::STORE_ID, $this->getstoreId());
		 		$second_table_name = $this->resourcees->getTableName('eav_attribute_option_value'); 
		$eav_attribute_option = $this->resourcees->getTableName('eav_attribute_option'); 
		 $this->brandcollection->getSelect()->joinInner(array('amshopbybrand_option' => $eav_attribute_option),
                                               '(main_table.value = amshopbybrand_option.option_id)');						
		$this->brandcollection->getSelect()->join(array('option' => $second_table_name),
                                               '(main_table.value = option.option_id)');
		 $this->brandcollection->getSelect()->columns(
            'IF(main_table.title != \'\', main_table.title, option.value) as title'
        );
        $this->brandcollection->getSelect()->columns(
            'IF(main_table.meta_title != \'\', main_table.meta_title, option.value) as meta_title'
        );
        $this->brandcollection->getSelect()->group('option_setting_id');
		 if ($this->getRequest()->getParam('sort') && $this->getRequest()->getParam('order')) {
			 
			     $this->brandcollection->getSelect()->order(array($this->getRequest()->getParam('sort').' '.$this->getRequest()->getParam('order')));
		 }
				}
		  return $this->brandcollection;
		  }
    }
		public function getstoreId()
    {
			return $this->_storeManager->getStore()->getId();
	}		
	public function getImageUrl()
    {
		$destinationFolder = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $destinationFolder;
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
}
