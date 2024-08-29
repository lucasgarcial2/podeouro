<?php
/**
 * Purpletree_Marketplace EditProduct
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

class EditProduct extends \Magento\Framework\View\Element\Template
{
    /**
     * Constructor
     *
     * @param \Magento\Directory\Model\Config\Source\Country
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Purpletree\Marketplace\Model\AttributesList
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Purpletree\Marketplace\Model\ResourceModel\Category
     * @param \Magento\Framework\View\Element\Template\Context
     * @param \Magento\Catalog\Model\Category
     * @param \Magento\Framework\Registry
     * @param \Magento\Catalog\Helper\Category
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State
     * @param \Magento\Directory\Model\Currency
     * @param array $data
     */

    public $entityAttribute;
    public $moduleManager;
    public $attributeSetRepositoryInterface;
    public $attributeGroup;
    public $categorymodel;
    public $countryHelper;
    public $productTaxClassSource;
    public $attributeFactory;
    public $helper;
    public $_link;
    public $_sampleModel;
    public $_downloadableFile;
    public $categorycustom;
    public $attributeRepository;
    public $option;
    public $_categoryHelper;
    public $categoryFlatConfig;
    public $coreRegistry;
    public $currency;
    public $eavConfig;
    public $_productMetadataInterface;       

    public function __construct(
        \Magento\Directory\Model\Config\Source\Country $countryHelper,
        \Magento\Catalog\Model\Product\AttributeSet\Options $option,
        \Purpletree\Marketplace\Model\AttributesList $attributeRepository,
        \Purpletree\Marketplace\Helper\Data $helper,
        \Magento\Downloadable\Helper\File $downloadableFile,
        \Purpletree\Marketplace\Model\ResourceModel\Category $categorycustom,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\Category $categorymodel,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Tax\Model\TaxClass\Source\Product $productTaxClassSource,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Magento\Directory\Model\Currency $currency,
        \Magento\Downloadable\Model\Sample $sampleModel,
		\Magento\Catalog\Model\ResourceModel\Eav\AttributeFactory $attributeFactory,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
		\Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSetRepositoryInterface,
		\Magento\Eav\Model\Entity\Attribute\Group $attributeGroup,
		\Magento\Framework\Module\Manager $moduleManager,
		\Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Downloadable\Model\Link $link,
		\Magento\Eav\Model\Config $eavConfig,
        array $data = []
    ) {
        $this->entityAttribute        			= $entityAttribute;
			$this->moduleManager 					= $moduleManager;
        $this->attributeSetRepositoryInterface  = $attributeSetRepositoryInterface;
        $this->attributeGroup        			= $attributeGroup;
        $this->categorymodel        			= $categorymodel;
        $this->countryHelper        			= $countryHelper;
        $this->productTaxClassSource 			= $productTaxClassSource;
		$this->attributeFactory 				= $attributeFactory;
        $this->helper 							= $helper;
        $this->_link 							= $link;
        $this->_sampleModel 					= $sampleModel;
        $this->_downloadableFile 				= $downloadableFile;
        $this->categorycustom 					= $categorycustom;
        $this->attributeRepository  			= $attributeRepository;
        $this->option              				= $option;
        $this->_categoryHelper      			= $categoryHelper;
        $this->categoryFlatConfig   			= $categoryFlatState;
        $this->coreRegistry         			= $coreRegistry;
        $this->currency             			= $currency;
		 $this->eavConfig = $eavConfig;
        $this->_productMetadataInterface        = $productMetadataInterface;
        parent::__construct($context, $data);
    }
    public function getVersion()
    {
        return $this->_productMetadataInterface->getVersion();
    }
    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    } 
	public function getattributeoptionsss($attribute_id)
    {
      $attribute = $this->attributeFactory->create();
      return $attribute->load($attribute_id);
    }
    public function getTaxData()
    {
        return $this->productTaxClassSource->getAllOptions(false);
    }
    public function getWebsiteRepository()
    {
        return $this->_storeManager->getWebsites();
    }
    public function getLinksTitle()
    {
        return $this->getProduct()->getId() &&
            $this->getProduct()->getTypeId() ==
            'downloadable' ? $this->getProduct()->getLinksTitle() : $this->_scopeConfig->getValue(
                \Magento\Downloadable\Model\Link::XML_PATH_LINKS_TITLE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
    }
    public function getSamplesTitle()
    {
        return $this->getProduct()->getId()
        && $this->getProduct()->getTypeId() == 'downloadable' ? $this->getProduct()->getSamplesTitle() :
        $this->_scopeConfig->getValue(
            \Magento\Downloadable\Model\Sample::XML_PATH_SAMPLES_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function dataHelper()
    {
        return $this->helper;
    }
        /**
         * Return true if price in website scope
         *
         * @return bool
         * @SuppressWarnings(PHPMD.BooleanGetMethodName)
         */
    public function getIsPriceWebsiteScope()
    {
        $scope = (int)$this->_scopeConfig->getValue(
            \Magento\Store\Model\Store::XML_PATH_PRICE_SCOPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($scope == \Magento\Store\Model\Store::PRICE_SCOPE_WEBSITE) {
            return true;
        }
        return false;
    }
        /**
         * Return formatted price with two digits after decimal point
         *
         * @param float $value
         * @return string
         */
    public function getPriceValue($value)
    {
        return number_format($value, 2, null, '');
    }
        /**
         * Return array of links
         *
         * @return array
         * @SuppressWarnings(PHPMD.CyclomaticComplexity)
         * @SuppressWarnings(PHPMD.NPathComplexity)
         */
    public function getLinkData()
    {
        $linkArr = [];
        if ($this->getProduct()->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return $linkArr;
        }
        $links = $this->getProduct()->getTypeInstance()->getLinks($this->getProduct());
        $priceWebsiteScope = $this->getIsPriceWebsiteScope();
        $fileHelper = $this->_downloadableFile;
        foreach ($links as $item) {
            $tmpLinkItem = [
                'link_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'price' => $this->getPriceValue($item->getPrice()),
                'number_of_downloads' => $item->getNumberOfDownloads(),
                'is_shareable' => $item->getIsShareable(),
                'link_url' => $item->getLinkUrl(),
                'link_type' => $item->getLinkType(),
                'sample_file' => $item->getSampleFile(),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            $linkFile = $item->getLinkFile();
            if ($linkFile) {
                $file = $fileHelper->getFilePath($this->_link->getBasePath(), $linkFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $item->getId(), 'type' => 'link', '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $linkFile
                    ) . '</a>';
                    $tmpLinkItem['file_save'] = [
                        [
                            'file' => $linkFile,
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            $sampleFile = $item->getSampleFile();
            if ($sampleFile) {
                $file = $fileHelper->getFilePath($this->_link->getBaseSamplePath(), $sampleFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/link',
                        ['id' => $item->getId(), 'type' => 'sample', '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $sampleFile
                    ) . '</a>';
                    $tmpLinkItem['sample_file_save'] = [
                        [
                            'file' => $item->getSampleFile(),
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            if ($item->getNumberOfDownloads() == '0') {
                $tmpLinkItem['is_unlimited'] = ' checked="checked"';
            }
            if ($this->getProduct()->getStoreId() && $item->getStoreTitle()) {
                $tmpLinkItem['store_title'] = $item->getStoreTitle();
            }
            if ($this->getProduct()->getStoreId() && $priceWebsiteScope) {
                $tmpLinkItem['website_price'] = $item->getWebsitePrice();
            }
            $linkArr[] = $tmpLinkItem;
        }
        return $linkArr;
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
     * Seller Categories
     *
     * @return Seller Categories
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
                 $catarry = $this->helper->getGeneralConfig('general/allow_category_seller');
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
     * Seller ID
     *
     * @return Seller ID
     */
    public function sellerid()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }
        
    /**
     * Get parent category identifier
     *
     * @return int
     */
    public function loadcategory($categoryId = false)
    {
          return $this->categorymodel->load($categoryId);
    }
    
    /**
     * Parent Categories
     *
     * @return Parent Categories
     */
    public function getParentId($categoryid)
    {
        $category = $this->loadcategory($categoryid);
        if ($category->getParentCategory()) {
            $parentid[] = $category->getParentCategory()->getId();
            $this->getParentId($category->getParentCategory()->getId());
        }
        return $parentid;
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
                    $checked = '';
                    if (in_array($childrenCategory->getId(), $this->getProduct()->getCategoryIds())) {
                        $checked = 'checked';
                    }
                    if (in_array($childrenCategory->getId(), $allowedcats)) {
                        $output .= '<li><label for="catnoselect'.$childrenCategory->getId().'">'.$childrenCategory->getName().'</label><input '.$checked.' value="'.$childrenCategory->getId().'" name="category[]" type="checkbox" id="cate'.$childrenCategory->getId().'" /><input class="catnoselect" '.$checked.' type="checkbox" id="catnoselect'.$childrenCategory->getId().'" />';
                        $output .= $this->getChildCategoriesLoop($childrenCategory, $allowedcats);
                    }
                }
                  $output .= '</ul>';
            }
        }
         return $output;
    }
    
    /**
     * Child Categories
     *
     * @return Child Categories
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
     * Country List
     *
     * @return Country List
     */
    public function getCountry()
    {
        return $this->countryHelper->toOptionArray();
    }
    
    /**
     * Attributes Label
     *
     * @return Attributes Label
     */
    public function getAttribvaluebyLabel($label, $attribid)
    {
        $attributeRepository = $this->attributeRepository->getAttributes();
        foreach ($attributeRepository->getItems() as $attribute) {
            if ($attribute->getId() == $attribid) {
                if (! empty($attribute->getSource()->getAllOptions(false))) {
                    foreach ($attribute->getSource()->getAllOptions(false) as $option) {
                        if ($option['label']==$label) {
                            return $option['value'];
                        }
                    }
                }
            }
        }
    }
    
    /**
     * All Attributes
     *
     * @return All Attributes
     */
    public function getAllAttributes()
    {
        $attributeRepository = $this->attributeRepository->getAttributes();
        $attributes = [];
        foreach ($attributeRepository->getItems() as $attribute) {
            if (! empty($attribute->getSource()->getAllOptions(false))) {
                if (null != $this->validateSeller($attribute->getAttributeCode())) {
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
     * @return Pager Html
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
     * Product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }
    
    /**
     * Childs
     *
     * @return Childs
     */
    public function getChilds()
    {
			if(!$this->coreRegistry->registry('seller_products')) {
				$this->coreRegistry->register('seller_products', '1');
			}
        $product = $this->coreRegistry->registry('current_product');
        $productTypeInstance = $product->getTypeInstance();
        $childpro = $productTypeInstance->getUsedProducts($product);
		$this->coreRegistry->unregister('seller_products');
        return $childpro;
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
        // array_pop($exploded_data);
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
       * Retrieve samples array
       *
       * @return array
       */
    public function getSampleData()
    {
        $samplesArr = [];
        if ($this->getProduct()->getTypeId() !== \Magento\Downloadable\Model\Product\Type::TYPE_DOWNLOADABLE) {
            return $samplesArr;
        }
        $samples = $this->getProduct()->getTypeInstance()->getSamples($this->getProduct());
        $fileHelper = $this->_downloadableFile;
        foreach ($samples as $item) {
            $tmpSampleItem = [
                'sample_id' => $item->getId(),
                'title' => $this->escapeHtml($item->getTitle()),
                'sample_url' => $item->getSampleUrl(),
                'sample_type' => $item->getSampleType(),
                'sort_order' => $item->getSortOrder(),
            ];

            $sampleFile = $item->getSampleFile();
            if ($sampleFile) {
                $file = $fileHelper->getFilePath($this->_sampleModel->getBasePath(), $sampleFile);

                $fileExist = $fileHelper->ensureFileInFilesystem($file);

                if ($fileExist) {
                    $name = '<a href="' . $this->getUrl(
                        'adminhtml/downloadable_product_edit/sample',
                        ['id' => $item->getId(), '_secure' => true]
                    ) . '">' . $fileHelper->getFileFromPathFile(
                        $sampleFile
                    ) . '</a>';
                    $tmpSampleItem['file_save'] = [
                        [
                            'file' => $sampleFile,
                            'name' => $name,
                            'size' => $fileHelper->getFileSize($file),
                            'status' => 'old',
                        ],
                    ];
                }
            }

            if ($this->getProduct() && $item->getStoreTitle()) {
                $tmpSampleItem['store_title'] = $item->getStoreTitle();
            }
            $samplesArr[] = $tmpSampleItem;
        }

        return $samplesArr;
    }
	public function getAdditionalAttributes() {
		$attributeSetId 			= $this->getProduct()->getAttributeSetId();//your_attributeSetId
		//$attributeSet 				= $this->_objectManager->create('Magento\Eav\Api\AttributeSetRepositoryInterface');
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
					} else {
					}
				} else {
							$inside = '1';
				}
			if($valueAtt['attribute_code'] == 'cost' || $valueAtt['attribute_code'] == 'seller_id' || $valueAtt['attribute_code'] == $this->getAmastyShopbyBrandcode() || $valueAtt['attribute_code'] == 'is_seller_product' || $valueAtt['frontend_model'] == 'Magento\Catalog\Model\Product\Attribute\Frontend\Image' || $valueAtt['attribute_code'] == 'product_designer_status' || $inside == '1') {
					unset($allattributesofSet[$keyAtt]);
			}
		}
		return array_merge($allattributesofSet,$this->getAmastyShopbyBrand());
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
