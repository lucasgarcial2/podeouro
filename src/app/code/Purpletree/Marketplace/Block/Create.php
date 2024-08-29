<?php
/**
 * Purpletree_Marketplace Create
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

class Create extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Directory\Model\Config\Source\Country
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Tax\Model\TaxClass\Source\Product
     * @param \Purpletree\Marketplace\Model\AttributesList
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Category
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Catalog\Helper\Category
     * @param \Magento\Framework\Registry
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State
     * @param \Magento\Directory\Model\Currency
     * @param array $data
     */

        public $helper;
		public $moduleManager;
		public $eavConfig;
		public $attributeFactory;
		public $attributeSetRepositoryInterface;
		public $entityAttribute;
        public $countryHelper;
        public $coreRegistry;
        public $categorycustom;
        public $productTaxClassSource;
        public $attributeRepository;
        public $option;
        public $_categoryHelper;
        public $categoryFlatConfig;
        public $currency;
        public $_productMetadataInterface;
		   public $categorymodel;

    public function __construct(
        \Magento\Directory\Model\Config\Source\Country $countryHelper,
        \Magento\Catalog\Model\Product\AttributeSet\Options $option,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
        \Purpletree\Marketplace\Helper\Data $helper,
        \Purpletree\Marketplace\Model\ResourceModel\Category $categorycustom,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
		\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepositoryInterface,
        \Magento\Directory\Model\Currency $currency,
		\Magento\Eav\Model\Entity\Attribute $entityAttribute,
		\Magento\Catalog\Model\Category $categorymodel,
				\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        array $data = []
    ) {
        $this->helper 							= $helper;
		$this->moduleManager 					= $moduleManager;
		 $this->eavConfig = $eavConfig;
		$this->attributeFactory 				= $attributeFactory;
		$this->attributeSetRepositoryInterface  = $attributeSetRepositoryInterface;
		$this->entityAttribute        			= $entityAttribute;
        $this->countryHelper 					= $countryHelper;
        $this->coreRegistry         			= $coreRegistry;
        $this->categorycustom					= $categorycustom;
        $this->productTaxClassSource 			= $productTaxClassSource;
        $this->attributeRepository 				= $attributeRepository;
        $this->option 							= $option;
        $this->_categoryHelper 					= $categoryHelper;
        $this->categoryFlatConfig 				= $categoryFlatState;
        $this->currency 						= $currency;
        $this->_productMetadataInterface        = $productMetadataInterface;
		   $this->categorymodel        			= $categorymodel;
        parent::__construct($context, $data);
    }
    public function getVersion()
    {
        return $this->_productMetadataInterface->getVersion();
    }
    /**
     * Country List
     *
     * @return Country List
     */
    public function getTaxData()
    {
        return $this->productTaxClassSource->getAllOptions(false);
    }

    public function getCountry()
    {
        return $this->countryHelper->toOptionArray();
    }
    public function dataHelper()
    {
        return $this->helper;
    }

/**
 * Return categories helper
 */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }

    /**
     * Retrieve current store categories
     *
     * @param bool|string $sorted
     * @param bool $asCollection
     * @param bool $toLoad
     * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
     */
    public function getStoreCategories($sorted = false, $asCollection = false, $toLoad = true)
    {
        return $this->_categoryHelper->getStoreCategories($sorted, $asCollection, $toLoad);
    }
    
    /**
     * Seller Attribute
     *
     * @return Seller Attribute
     */
    
    public function getSellerCategories()
    {
        
        $listcats = $this->categorycustom->getSellerCatids($this->sellerid());
        $catarry = [];
        if (!empty($listcats)) {
            foreach ($listcats as $catt) {
                $catarry[] = $catt['category_id'];
            }
        } else {
                 $catarry = $this->dataHelper()->getGeneralConfig('general/allow_category_seller');
                $catarry = explode(',', $catarry);
            if (!is_array($catarry)) {
				 //$catarry[] = $catarry; 
				$catarry1[] = $catarry;
            } else {
				$catarry1 = $catarry;
			}
			foreach($catarry1 as $catId) {
					$subCategory = $this->categorymodel->load($catId);
					$catarry33 = $subCategory->getAllChildren();
					$catarry44 = explode(',', $catarry33);
					foreach($catarry44 as $rere) {
						$catarry[] = $rere;
					}
				}
        }
        return $catarry;
    }
    /**
     * Retrieve child store categories
     *
     */
    public function getChildCategoriesLoop($category, $allowedcats = [])
    {
        $output = "";
        if ($childrenCategories = $this->getChildCategories($category)) {
            if (!empty($childrenCategories)) {
                $output = "<ul>";
                foreach ($childrenCategories as $childrenCategory) {
                    if (in_array($childrenCategory->getId(), $allowedcats)) {
                        $output .= '<li><label for="catnoselect'.$childrenCategory->getId().'">'.$childrenCategory->getName().'</label><input value="'.$childrenCategory->getId().'" name="category[]" type="checkbox" id="cate'.$childrenCategory->getId().'" /><input class="catnoselect bb" type="checkbox" id="catnoselect'.$childrenCategory->getId().'" />';
                        $output .= $this->getChildCategoriesLoop($childrenCategory, $allowedcats);
                    }
                }
                $output .= '</ul>';
            }
        }
        return $output;
    }
    
    /**
     * Child Attribute
     *
     * @return Child Attribute
     */
    public function getChildCategories($category)
    {
        if ($this->categoryFlatConfig->isFlatEnabled() && $category->getUseFlatResource()) {
            $subcategories = (array)$category->getChildrenNodes();
        } else {
            $subcategories = $category->getChildren();
        }
        return $subcategories;
    }
    
    /**
     * All Attribute
     *
     * @return All Attribute
     */
    public function getAllAttributes($attributeType)
    {
          $attributeRepository = $this->attributeRepository->getAttributes();
          $attributes = [];
        foreach ($attributeRepository->getItems() as $attribute) {
            if (! empty($attribute->getSource()->getAllOptions(false))) {
                if (null !== $this->validateSeller($attribute->getAttributeCode())) {
                    $attributes[] = [
                    'id' => $attribute->getId(),
                    'code' => $this->getAttriCode($attribute->getAttributeCode()),
                    'label' => $attribute->getFrontendLabel(),
                    'IsRequired' => (($attribute->getIsRequired() == 1)? 'Yes' : 'No'),
                    'IsSystemDefined' => (($attribute->getIsUserDefined() == 1)? 'Yes' : 'No'),
                    'IsVisible' => (($attribute->getIsVisible() == 1)? 'Yes' : 'No'),
                    'scope' => $attribute->getScope(),
                    'IsSearchable' => (($attribute->getIsSearchable() == 1)? 'Yes' : 'No'),
                    'IsComparable' => (($attribute->getIsComparable() == 1)? 'Yes' : 'No'),
                    ];
                }
            }
        }
     
        return $attributes;
    }
    
    /**
     * Attribute Code
     *
     * @return Attribute Code
     */
    public function getAttriCode($attribute_code)
    {
		return $attribute_code;
        //$exploded_data = explode("_seller_", $attribute_code);
         //array_pop($exploded_data);
		//return implode('_seller_',$exploded_data); 
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
        } else {
			return $attribute_code;
		}
    }
    
    /**
     * Seller Id
     *
     * @return Seller Id
     */
    public function sellerid()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }
    
    /**
     * Product Type
     *
     * @return Product Type
     */
    public function getProductType()
    {
        return $this->coreRegistry->registry('product_type');
    }
    
    /**
     * Attribute Set
     *
     * @return Attribute Set
     */
    public function getAttributeSet()
    {
        return $this->coreRegistry->registry('attribute_set');
    }
    
    /**
     * Currency Symbol
     *
     * @return Currency Symbol
     */
    public function getCurrentCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
    
    /**
     * Attribute set List
     *
     * @return Attribute set List
     */
    public function getOption()
    {
        return $this->option->toOptionArray();
    }
    
    /**
     * Pager Html
     *
     * @return  Pager Html
     */
    public function getPagerHtml()
    {
        return $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager',
            'purpletree.marketplace.record.pager'
        )->setCollection(
            $this->getAllAttributes() // assign collection to pager
        );
    }
    public function getWebsiteRepository()
    {
        return $this->_storeManager->getWebsites();
    }
	public function getAdditionalAttributes($attributeSetId) {
		$attributeSetRepository 	= $this->attributeSetRepositoryInterface->get($attributeSetId);
		$attribute_set_name 		= $attributeSetRepository->getAttributeSetName();
		$attributeCollection = $this->entityAttribute->getCollection();
		$attributeCollection->setAttributeSetFilter($attributeSetId);
		$attributeCollection->addFieldToFilter('is_user_defined','1');
		$allattributesofSet = $attributeCollection->getData();
		$allcustomAttributes = array();
		foreach($allattributesofSet as $keyAtt => $valueAtt) {
			 $atttrr = $valueAtt['attribute_code'];
				$inside = '0';
				if (strpos($atttrr, 'seller_') !== false) {
				   $explodedattrib = explode('_seller_',$atttrr);
					if (isset($explodedattrib[1])) {
						if(end($explodedattrib) != $this->sellerid()) {
							$inside = '1';
						}
					}
				} else {
					$inside = '1';
				}
				 //array_pop($exploded_data);
				// return implode('_seller_',$exploded_data); 
			if($valueAtt['attribute_code'] == 'cost' || $valueAtt['attribute_code'] == 'seller_id' || $valueAtt['attribute_code'] == $this->getAmastyShopbyBrandcode() || $valueAtt['attribute_code'] == 'is_seller_product' || $valueAtt['frontend_model'] == 'Magento\Catalog\Model\Product\Attribute\Frontend\Image' || $valueAtt['attribute_code'] == 'product_designer_status' || $inside == '1') {
					unset($allattributesofSet[$keyAtt]);
			}
		}
		return array_merge($allattributesofSet,$this->getAmastyShopbyBrand());
	}
		public function getattributeoptionsss($attribute_id)
    {
      $attribute = $this->attributeFactory->create();
      return $attribute->load($attribute_id);
    }
	 public function getAmastyShopbyBrandcode()
    {
		$brandcode = '';
		if ($this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand')) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		 $brandcode = $objectManager->get('Amasty\ShopbyBase\Model\AllowedRoute')->getBrandCode();
		}
		return $brandcode;
	}
	    public function getAmastyShopbyBrand()
    {
		$attributes = array();
       if ($this->moduleManager->isOutputEnabled('Amasty_ShopbyBrand')) {
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		 $brand = $objectManager->get('Amasty\ShopbyBase\Model\AllowedRoute')->getBrandCode();
		$attribute11 = $this->eavConfig->getAttribute('catalog_product', $brand);
		 $attribute = $this->attributeFactory->create();
         $attribute->load($attribute11->getAttributeId());
		 $attributes[] = [
                    'attribute_id' => $attribute->getId(),
                    'entity_type_id' => $attribute->getEntityTypeId(),
                    'attribute_code' => $attribute->getAttributeCode(),
                    'attribute_model' => $attribute->getAttributeModel(),
                    'backend_model' => $attribute->getBackendModel(),
                    'backend_type' => $attribute->getBackendType(),
                    'backend_table' => $attribute->getBackendTable(),
                    'frontend_model' => $attribute->getFrontendModel(),
                    'frontend_input' => $attribute->getFrontendInput(),
                    'frontend_label' => $attribute->getFrontendLabel(),
                    'frontend_class' => $attribute->getFrontendClass(),
                    'source_model' => $attribute->getSourceModel(),
					'is_required' => $attribute->getIsRequired(),
                    'is_user_defined' => $attribute->getIsUserDefined(),
                    'default_value' => $attribute->getDefaultValue(),
                    'is_unique' => $attribute->getIsUnique(),
                    'note' => $attribute->getNote(),
                    'entity_attribute_id' => $attribute->getEntityAttributeId(),
                    'attribute_set_id' => $attribute->getAttributeSetId(),
                    'attribute_group_id' => $attribute->getAttributeGroupId(),
                    'sort_order' => $attribute->getSortOrder(),
                    ];
	   }
	   
	return $attributes;
    }
}
