<?php

/**
 * Purpletree_Marketplace SaveSellerProducts
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
 
namespace Purpletree\Marketplace\Controller\Adminhtml\Index;

class SaveSellerProducts extends \Magento\Framework\App\Action\Action
{

    /**
     * @param \Magento\Framework\App\Action\Context $context
     */

     public $jsonHelper;
     public $productAction;
     public $productcollection;
     public $_collectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\ResourceModel\Product\Action $productAction,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productcollection
    ) {
    
        $this->jsonHelper = $jsonHelper;
        $this->productAction = $productAction;
        $this->productcollection = $productcollection;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
    /**
     * Default customer account page
     *
     * @return void
     */
    public function execute()
    {
        $result['success']='false';
        $products = $this->getRequest()->getpost('products');
        $seller = $this->getRequest()->getpost('seller');
        $value = $this->getRequest()->getpost('unassign');
    
        if (isset($seller)) {
            if (!empty($products)) {
                if ($value != 0) {
                    $products = array_diff($products, $this->_getSelectedProducts($seller));
                }
            }
            if (!empty($products)) {
                $this->updatesellerProducts($value, $products, $seller);
                $result['success']='true';
            }
            $this->getResponse()->representJson(
                $this->jsonHelper->jsonEncode($result)
            );
        }
    }
    
    /**
     * Seller Products
     *
     * @return Seller Products
     */
    protected function updatesellerProducts($value, $products, $seller)
    {
        
        $productsnew = [];
        foreach ($products as $pro) {
            $productsnew[] = explode('id_', $pro)[1];
        }
            $collection = $this->productcollection->create();
            $collection->addAttributeToSelect('entity_id');
            $collection->addAttributeToFilter('entity_id', ['in' => $productsnew]);
            $_action = $this->productAction;
        if ($value == 0) {
                $seller = '';
        }
                    $prodids = [];
        foreach ($collection as $proo) {
            $prodids[] = $proo->getId();
        }
        if (!empty($prodids)) {
                    $_action->updateAttributes($prodids, ['is_seller_product' => $value], 0);
                    $_action->updateAttributes($prodids, ['seller_id' => $seller], 0);
        }
    }
    
    /**
     * Seller Collection
     *
     * @return Collection
     */

    protected function _getSelectedCollection($seller)
    {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter('seller_id', $seller);
        return $collection;
    }

    /**
     * Products
     *
     * @return Products
     */
    protected function _getSelectedProducts($seller)
    {
        $selectedCollection = $this->_getSelectedCollection($seller);
        $productIds = [];
        foreach ($selectedCollection as $_selectedProducts) {
                $productIds[] = 'id_'.$_selectedProducts->getId();
        }
        return $productIds;
    }
}
