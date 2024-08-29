<?php
/**
 * Purpletree_Marketplace Category
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
namespace Purpletree\Marketplace\Model\Config\Backend;

class Category extends \Magento\Framework\App\Config\Value
{
    public function beforeSave()
    {
        $label = $this->getData('field_config/label');
        if ($this->getValue() && count($this->getValue()) == 1 && $this->getValue()[0] == 2) {
            $this->setValue($this->_toArray());
            parent::beforeSave();
        }
    }
    public function getCategoryCollection($isActive = true, $level = false, $sortBy = false, $pageSize = false)
    {
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $categoryCollectionFactory   = $objectManager
        ->get('\Magento\Catalog\Model\ResourceModel\Category\CollectionFactory');
        $collection = $categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
        
        // select categories of certain level
        if ($level) {
            $collection->addLevelFilter($level);
        }
  
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
  
        // select certain number of categories
        if ($pageSize) {
            $collection->setPageSize($pageSize);
        }
  
        return $collection;
    }
    private function _toArray()
    {
        $categories = $this->getCategoryCollection(true, false, false, false);
        $catagoryList = [];
        foreach ($categories as $category) {
            $catagoryList[] = $category->getEntityId();
        }
        return $catagoryList;
    }
}
