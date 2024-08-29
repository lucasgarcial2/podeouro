<?php
/**
 * Purpletree_Marketplace Products
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

class Products extends \Magento\Framework\View\Element\Template
{
    /**
     * Core Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $productCollection;
     
     /**
      * Constructor
      *
      * @param \Magento\Framework\View\Element\Template\Context
      * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
      * @param \Magento\CatalogInventory\Api\StockStateInterface
      * @param \Magento\Directory\Model\Currency
      * @param array $data
      */
      public $productCollectionFactory;
      public $currencyprice;
      public $_sstore;
      public $attributeSet;
      public $coreRegistry;
      public $stockItemRepository;
      public $_productMetadataInterface;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\CatalogInventory\Api\StockStateInterface $stockItemRepository,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\System\Store $_sstore,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
        \Magento\Framework\Pricing\Helper\Data $currencyprice,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
         $this->currencyprice = $currencyprice;
        $this->_sstore = $_sstore;
        $this->attributeSet = $attributeSet;
        $this->coreRegistry = $coreRegistry;
        $this->stockItemRepository = $stockItemRepository;
         $this->_productMetadataInterface             =       $productMetadataInterface;
        parent::__construct($context, $data);
    }
      
    protected function _prepareLayout()
    {
		
        parent::_prepareLayout();
        if ($this->getProductCollection()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'seller.products.pager'
            )->setCollection(
                $this->getProductCollection()
            );
            $this->setChild('pager', $pager);
            $this->getProductCollection();
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
     * Product List
     *
     * @return Product List
     */
    public function getSellerId()
    {
        return $this->coreRegistry->registry('current_customer_id');
    }
    public function getattributeName($setid)
    {
		if($setid) {
         $attributeSetRepository = $this->attributeSet->get($setid);
         return $name = $attributeSetRepository->getAttributeSetName();
         /* $namm = explode('_seller_', $name);
        if (isset($namm[1])) {
			if(end($namm) == $this->getSellerId()) {
           array_pop($namm);
			$name = implode('_seller_',$namm);
			}
        }
             return $name; */
		}
    }
    public function getWebsiteName($id)
    {
        return $this->_sstore->getWebsiteName($id);
    }
    
    /**
     * Product Collection
     *
     * @return Product Collection
     */
    public function getProductCollection()
    {
			if(!$this->coreRegistry->registry('seller_products')) {
				$this->coreRegistry->register('seller_products', '1');
			}
        $data = $this->getRequest()->getPostValue();
        if (!$this->productCollection) {
            if ($this->getRequest()->isAjax()) {
                $productName        = isset($data['product_name'])?$data['product_name']:'';
                $productSku             = isset($data['product_sku'])?$data['product_sku']:'';
                $sellerId           = $this->getSellerId();
                $this->productCollection = $this->getProductCollectionAjax($productName, $productSku, $sellerId);
            } else {

                $this->productCollection = $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('seller_id', $this->getSellerId())
                ->addAttributeToSort('entity_id', 'desc')
                ->load();
                            //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
             $this->productCollection->clear();
                $fromAndJoin = $this->productCollection->getSelect()->getPart('FROM');
                $updatedfromAndJoin = [];
                foreach ($fromAndJoin as $key => $index) {
                    if ($key == 'stock_status_index') {
                        $index['joinType'] = 'left join';
                    }
                    $updatedfromAndJoin[$key] = $index;
                }
                if (!empty($updatedfromAndJoin)) {
                    $this->productCollection->getSelect()->setPart('FROM', $updatedfromAndJoin);
                }

                $where = $this->productCollection->getSelect()->getPart('where');
                $updatedWhere = [];
                foreach ($where as $key => $condition) {
						 if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                        if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                            $updatedWhere[] = $condition;
                        }
                    } else {
                        if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                            $updatedWhere[] = $condition;
                        }
                    }
                }
                if (!empty($updatedWhere)) {
                    $this->productCollection->getSelect()->setPart('where', $updatedWhere);
                } 
            }
        }
		if($this->coreRegistry->registry('seller_products')) {
			$this->coreRegistry->unregister('seller_products');
		}
        return $this->productCollection;
    }
    public function getProductCollectionAjax($productName, $productSku, $sellerId)
    {
		
        if ($productSku=='' && $productName=='') {
            $productCollection = $this->productCollectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('seller_id', $sellerId)
                                ->addAttributeToSort('entity_id', 'desc')
                                ->load();
                                //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
            $productCollection->clear();
            $fromAndJoin = $productCollection->getSelect()->getPart('FROM');
            $updatedfromAndJoin = [];
            foreach ($fromAndJoin as $key => $index) {
                if ($key == 'stock_status_index') {
                    $index['joinType'] = 'left join';
                }
                $updatedfromAndJoin[$key] = $index;
            }
            if (!empty($updatedfromAndJoin)) {
                $productCollection->getSelect()->setPart('FROM', $updatedfromAndJoin);
            }

            $where = $productCollection->getSelect()->getPart('where');
            $updatedWhere = [];
            foreach ($where as $key => $condition) {
                 if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                    if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                } else {
                    if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                }
            }
            if (!empty($updatedWhere)) {
                $productCollection->getSelect()->setPart('where', $updatedWhere);
            }
        } elseif ($productName=='') {
            $productCollection = $this->productCollectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('sku', ['like' => '%' . $productSku. '%'])
                                ->addAttributeToFilter('seller_id', $sellerId)
                                ->addAttributeToSort('entity_id', 'desc')
                                ->load();
                                //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
            $productCollection->clear();
            $fromAndJoin = $productCollection->getSelect()->getPart('FROM');
            $updatedfromAndJoin = [];
            foreach ($fromAndJoin as $key => $index) {
                if ($key == 'stock_status_index') {
                    $index['joinType'] = 'left join';
                }
                $updatedfromAndJoin[$key] = $index;
            }
            if (!empty($updatedfromAndJoin)) {
                $productCollection->getSelect()->setPart('FROM', $updatedfromAndJoin);
            }

            $where = $productCollection->getSelect()->getPart('where');
            $updatedWhere = [];
            foreach ($where as $key => $condition) {
                 if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                    if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                } else {
                    if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                }
            }
            if (!empty($updatedWhere)) {
                $productCollection->getSelect()->setPart('where', $updatedWhere);
            }
        } elseif ($productSku=='') {
            $productCollection = $this->productCollectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('name', ['like' => '%' . $productName. '%'])
                                ->addAttributeToFilter('seller_id', $sellerId)
                                ->addAttributeToSort('entity_id', 'desc')
                                ->load();
                                //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
            $productCollection->clear();
            $fromAndJoin = $productCollection->getSelect()->getPart('FROM');
            $updatedfromAndJoin = [];
            foreach ($fromAndJoin as $key => $index) {
                if ($key == 'stock_status_index') {
                    $index['joinType'] = 'left join';
                }
                $updatedfromAndJoin[$key] = $index;
            }
            if (!empty($updatedfromAndJoin)) {
                $productCollection->getSelect()->setPart('FROM', $updatedfromAndJoin);
            }

            $where = $productCollection->getSelect()->getPart('where');
            $updatedWhere = [];
            foreach ($where as $key => $condition) {
                 if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                    if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                } else {
                    if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                }
            }
            if (!empty($updatedWhere)) {
                $productCollection->getSelect()->setPart('where', $updatedWhere);
            }
        } else {
            $productCollection = $this->productCollectionFactory->create()
                                ->addAttributeToSelect('*')
                                ->addAttributeToFilter('name', ['like' => '%' . $productName. '%'])
                                ->addAttributeToFilter('sku', ['like' => '%' . $productSku. '%'])
                                ->addAttributeToFilter('seller_id', $sellerId)
                                ->addAttributeToSort('entity_id', 'desc')
                                ->load();
                                //Fix : Disabled product not coming in product collection in ver-Mage2.2.2
            $productCollection->clear();
            $fromAndJoin = $productCollection->getSelect()->getPart('FROM');
            $updatedfromAndJoin = [];
            foreach ($fromAndJoin as $key => $index) {
                if ($key == 'stock_status_index') {
                    $index['joinType'] = 'left join';
                }
                $updatedfromAndJoin[$key] = $index;
            }
            if (!empty($updatedfromAndJoin)) {
                $productCollection->getSelect()->setPart('FROM', $updatedfromAndJoin);
            }
            $where = $productCollection->getSelect()->getPart('where');
            $updatedWhere = [];
            foreach ($where as $key => $condition) {
                if ($this->_productMetadataInterface->getVersion() != "2.3.0") {
                    if (strpos($condition, 'stock_status_index.stock_status = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                } else {
                    if (strpos($condition, 'stock_status_index.is_salable = 1') === false) {
                        $updatedWhere[] = $condition;
                    }
                }
            }
            if (!empty($updatedWhere)) {
                $productCollection->getSelect()->setPart('where', $updatedWhere);
            }
        }
        return $productCollection;
    }
    /**
     * Currency
     *
     * @return Currency
     */
    public function convertToCurrency($price)
    {
        return $this->currencyprice->currency($price, true, false);
    }
    
    /**
     * Stock Quantity
     *
     * @return Stock Quantity
     */
    public function getStockQty($productId)
    {
        return $this->stockItemRepository->getStockQty($productId);
    }
}
