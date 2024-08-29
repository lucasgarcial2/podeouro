<?php
/**
 * Purpletree_Marketplace StoreView
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
namespace Purpletree\Marketplace\Controller\Index;

use Purpletree\Marketplace\Block\StoreViewDetail;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class StoreView extends Action
{
    /** @var  \Magento\Catalog\Model\ResourceModel\Product\Collection */
    protected $productCollection;

    public $collectionpro;
    public $pageFactory;
    public $_resource;
    public $storeDetails;
    public $storeManagerr;
    public $dataHelper;
    public $coreRegistry;
    public $resultForwardFactory;
    public $_resultPage;
  
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collectionpro,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Processdata $processdata,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\App\ResourceConnection $resource,
        \Purpletree\Marketplace\Model\ResourceModel\Seller $storeDetails
    ) {
     
        $this->collectionpro          = $collectionpro;
        $this->pageFactory          = $pageFactory;
		$this->_resource = $resource;
        $this->productCollection    = $collectionFactory->create();
        $this->storeDetails         = $storeDetails;
        $this->storeManagerr         = $storeManager;
        $this->dataHelper           = $dataHelper;
        $this->coreRegistry         = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $storeUrl = $this->getRequest()->getParam('store');
        $sellerId=$this->storeDetails->storeIdByUrl($storeUrl);
        if (!$sellerId) {
            $sellerId = $this->storeDetails->getSellerIdById($storeUrl);
        }
        $isseller = $this->storeDetails->isSeller($sellerId);
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$isseller || !$moduleEnable) {
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }
        $storeDeatls=$this->storeDetails->getStoreDetails($sellerId);
        $this->coreRegistry->register('store_url', $storeUrl);
         // obtain product collection.
       // $this->productCollection->addIdFilter(5); // do some filtering
            $this->productCollection->addAttributeToFilter('seller_id', $sellerId);
            $this->productCollection->addFieldToSelect('*');
            $this->productCollection->addAttributeToSelect('*');
			$this->productCollection->addStoreFilter($this->storeManagerr->getStore()->getId());
            $this->productCollection->addAttributeToFilter('visibility', 4);
            $this->productCollection->addAttributeToFilter('status', 1);
			if($this->collectionpro->isEnabledFlat()) {
			$purpletree_marketplace_stores = $this->_resource->getTableName("purpletree_marketplace_stores");
			$this->productCollection->getSelect()->join(array('pms' => $purpletree_marketplace_stores), "e.seller_id = pms.seller_id", array('pms.entity_idpts as cat_index_position'));
			}

        // get the custom list block and add our collection to it
        /** @var CustomList $list */
        $result = $this->pageFactory->create();
        $list = $result->getLayout()->getBlock('purpletree.marketplace.custom.products.list');
        $list->setProductCollection($this->productCollection);
        $list->setData('page_type', 'catalogsearch');
        $result->getConfig()->getTitle()->set($storeDeatls['store_name']);
        return $result; 
    }
}
