<?php

/**
 * Purpletree_Marketplace SellerCategories
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Software
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */

namespace Purpletree\Marketplace\Block\Adminhtml\Edit\Tab;

use Magento\Customer\Controller\RegistryConstants;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
 
class SellerCategories extends \Magento\Framework\View\Element\Template implements TabInterface
{
	 protected $_storeCategories = [];
	 
     /**
      * @param \Magento\Backend\Block\Template\Context $context
      * @param \Magento\Framework\Registry $registry
      * @param \Magento\Catalog\Helper\Category $categoryHelper
      * @param \Purpletree\Marketplace\Model\ResourceModel\Category $categorycustom
      * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState
      * @param \Magento\Catalog\Model\CategoryRepository $categoryRepository
      * @param \Purpletree\Marketplace\Helper\Data $dataHelper
      * @param array $data
      */

     public $_coreRegistry;
     public $_categoryFactory;
     public $categorycustom;
     public $_categoryHelper;
     public $categoryFlatConfig;
     public $dataHelper;
     public $customerRepository;
     public $customerFactory;
     public $_dataCollectionFactory;
     public $storeManagerr;
     public $scopeConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Category $categorycustom,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Framework\Data\CollectionFactory $dataCollectionFactory,
		\Magento\Customer\Model\CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository,
        array $data = []
    ) {
        $this->_coreRegistry 			= $registry;
		$this->_categoryFactory 		= $categoryFactory;
        $this->categorycustom  			= $categorycustom;
        $this->_categoryHelper      	= $categoryHelper;
        $this->categoryFlatConfig   	= $categoryFlatState;
        $this->dataHelper 				= $dataHelper;
        $this->customerRepository  		= $customerRepository;
        $this->customerFactory  		= $customerFactory;
		$this->_dataCollectionFactory 	= $dataCollectionFactory;
		$this->storeManagerr 			= $context->getStoreManager();
		$this->scopeConfig 				= $context->getScopeConfig();
        parent::__construct($context, $data);
    }
 
    /**
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Assign Categories');
    }
    /**
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Assign Categories');
    }
    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->getCustomerId()) {
            if ($this->getIsSeller() == 1) {
                return true;
            }
        }
        return false;
    }
            /**
             * @return string|null
             */
    public function getIsSeller()
    {
        $customer = $this->customerRepository->getById($this->getCustomerId());
        if (!empty($customer->getCustomAttribute('is_seller'))) {
            return $customer->getCustomAttribute('is_seller')->getValue();
        } else {
            return 0;
        }
    }
 
    /**
     * @return bool
     */
    public function isHidden()
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }
    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return '';
    }
    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl()
    {
    //replace the tab with the url you want
        return $this->getUrl('purpletree_marketplace/*/sellercategories', ['_current' => true]);
    }
    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded()
    {
        return true;
    }
     /**
      * Retrieve current store categories
      *
      * @param bool|string $sorted
      * @param bool $asCollection
      * @param bool $toLoad
      * @return \Magento\Framework\Data\Tree\Node\Collection|\Magento\Catalog\Model\Resource\Category\Collection|array
      */
    public function getStoreCategories()
    {
		$customerId 	 = $this->getCustomerId();
		$customer 		 = $this->customerFactory->create()->load($customerId);
		//fetch whole customer information
		$store_id 		 = $customer->getData('store_id');
		$parent 		 = $this->storeManagerr->getStore($store_id)->getRootCategoryId();
		return $this->getStoreCategori($parent);
		
    }
	    public function getStoreCategori($parent)
    {
		$sorted 		= false;
		$asCollection   = false;
		$toLoad 		= true;
        $cacheKey 		= sprintf('%d-%d-%d-%d', $parent, $sorted, $asCollection, $toLoad);
        if (isset($this->_storeCategories[$cacheKey])) {
            return $this->_storeCategories[$cacheKey];
        }

        /**
         * Check if parent node of the store still exists
         */
        $category = $this->_categoryFactory->create();
        /* @var $category ModelCategory */
        if (!$category->checkId($parent)) {
            if ($asCollection) {
                return $this->_dataCollectionFactory->create();
            }
            return [];
        }

        $recursionLevel = max(
            0,
            (int)$this->scopeConfig->getValue(
                'catalog/navigation/max_depth',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
        $storeCategories = $category->getCategories($parent, $recursionLevel, $sorted, $asCollection, $toLoad);

        $this->_storeCategories[$cacheKey] = $storeCategories;
        return $storeCategories;
    }

    /**
     * Retrieve child store categories
     *
     */
    public function getadminallowedcategories()
    {
        $allowedcats = [];
        if ($this->dataHelper->getGeneralConfig('general/allow_category_seller') != '') {
            $allowedcats = $this->dataHelper->getGeneralConfig('general/allow_category_seller');
            $allowedcats = explode(',', $allowedcats);
        }
        return $allowedcats;
    }
    public function getChildCategoriesLoop($category)
    {
		$output = '';
        if ($childrenCategories = $this->getChildCategories($category)) {
            if (!empty($childrenCategories)) {
                $output .= "<ul class='cattree'>";
                foreach ($childrenCategories as $childrenCategory) {
                    $checked = '';
                    if (in_array($childrenCategory->getId(), $this->getSellerCategories())) {
                        $checked = 'checked=checked';
                    }
                    $allowedcats[] = $childrenCategory->getId();
                    $output .= '<li><label for="catnoselect'.$childrenCategory->getId().'">'.$childrenCategory->getName().'</label><input class="catselect" form="customer_form" data-form-part="customer_form" '.$checked.' value="'.$childrenCategory->getId().'" name="seller[category][]" type="checkbox" id="cate'.$childrenCategory->getId().'" /><input class="catnoselect" '.$checked.' type="checkbox" id="catnoselect'.$childrenCategory->getId().'" />';
                    $output .= $this->getChildCategoriesLoop($childrenCategory);
                }
                    $output .= "</ul>";
            }
        }
         return $output;
    }

    /**
     * Retrieve seller categories
     *
     */
    public function getSellerCategories()
    {
        
        $listcats = $this->categorycustom->getSellerCatids($this->getCustomerId());
        $catarry = [];
        if (!empty($listcats)) {
            foreach ($listcats as $catt) {
                $catarry[] = $catt['category_id'];
            }
        }
        return $catarry;
    }
    
    /**
     * Retrieve child store categories
     *
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
}
