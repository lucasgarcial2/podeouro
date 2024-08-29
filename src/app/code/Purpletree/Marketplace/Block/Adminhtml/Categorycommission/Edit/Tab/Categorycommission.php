<?php
/**
 * Purpletree_Marketplace Categorycommission
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
 
namespace Purpletree\Marketplace\Block\Adminhtml\Categorycommission\Edit\Tab;

class Categorycommission extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    
    /**
     * constructor
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller $sellerinfo
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    
    public $_categoryHelper;
    public $categoryFlatConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $categoryFlatState,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_categoryHelper      = $categoryHelper;
        $this->categoryFlatConfig   = $categoryFlatState;
    }
    /**
     * Prepare form
     * @return $this
     */
    protected function _prepareForm()
    {
        $categorycommission = $this->_coreRegistry->registry('purpletree_marketplace_categorycommission');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('categorycommission');
        $form->setFieldNameSuffix('categorycommission');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Category Commission'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($categorycommission->getId()) {
            $fieldset->addField(
                'entity_id',
                'hidden',
                ['name' => 'entity_id']
            );
        }
        $categoryy = $fieldset->addField(
            'commission',
            'text',
            [
                'name'  => 'commission',
                'label' => __('Commission %'),
                'title' => __('Commission %'),
                'class' => 'validate-digits required-entry validate-greater-than-zero', //add multiple classess
                 'required' => true,
            ]
        );
        $categories = $this->getStoreCategories(true, false, true);
        $categoryydsd = '';
        foreach ($categories as $category) {
            if (!$category->getIsActive()) {
                continue;
            }
                $check = "";
            if ($categorycommission->getId()) {
                if ($categorycommission->getCategoryId() == $category->getId()) {
                    $check = "checked=checked";
                }
            }
                $categoryydsd .= '<li><label for="catnoselect'.$category->getId().'">'.$category->getName().'</label><input '.$check.' name="categorycommission[category_id]" data-ui-id="purpletree-marketplace-categorycommission-edit-tab-categorycommission-fieldset-element-radio-categorycommission-category-id" value="'.$category->getId().'" class="catselect" type="radio" id="cate'.$category->getId().'" /><input name="sssss" '.$check.' class="catnoselect" type="checkbox" id="catnoselect'.$category->getId().'" />'.$this->getChildCategoriesLoop($category, $categorycommission).'</li>';
        }
        $categoryy->setAfterElementHtml('<ul style="margin-top:20px;" class="productcategorytree">'.$categoryydsd.'</ul>');
        $categorycommissionData = $this->_session->getData('purpletree_marketplace_categorycommission_data', true);
        if ($categorycommissionData) {
            $categorycommission->addData($categorycommissionData);
        } else {
            if (!$categorycommission->getId()) {
                $categorycommission->addData($categorycommission->getDefaultValues());
            }
        }
        $form->addValues($categorycommission->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
    
    public function getTabLabel()
    {
        return __('Commission');
    }
    /**
     * Prepare title for tab
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }
    /**
     * Can show tab in tabs
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }
    /**
     * Tab is hidden
     * @return boolean
     */
    public function isHidden()
    {
        return false;
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
    public function getChildCategoriesLoop($category, $categorycommission)
    {
        if ($childrenCategories = $this->getChildCategories($category)) {
            if (!empty($childrenCategories)) {
                $output = "<ul class='cattree'>";
                foreach ($childrenCategories as $childrenCategory) {
                    $checked = '';
                    if ($categorycommission->getId()) {
                        if ($categorycommission->getCategoryId() == $childrenCategory->getId()) {
                            $checked = 'checked=checked';
                        }
                    }
                    $output .= '<li><label for="catnoselect'.$childrenCategory->getId().'">'.$childrenCategory->getName().'</label><input class="catselect" '.$checked.' value="'.$childrenCategory->getId().'" name="categorycommission[category_id]" data-ui-id="purpletree-marketplace-categorycommission-edit-tab-categorycommission-fieldset-element-radio-categorycommission-category-id" type="radio" id="cate'.$childrenCategory->getId().'" /><input name="sssss" class="catnoselect" '.$checked.' type="checkbox" id="catnoselect'.$childrenCategory->getId().'" />';
                    $output .= $this->getChildCategoriesLoop($childrenCategory, $categorycommission);
                }
                    $output .= "</ul>";
            }
        }
        return $output;
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
