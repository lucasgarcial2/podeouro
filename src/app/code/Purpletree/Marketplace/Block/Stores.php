<?php
/**
 * Purpletree_Marketplace Stores
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

class Stores extends \Magento\Framework\View\Element\Template
{
    protected $sellers;
    protected $customerGroups;
    
    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\Product\AttributeSet\Options
     * @param \Magento\Eav\Api\AttributeRepositoryInterface
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Registry
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\View\Element\Template\Context
     * @param array $data
     */
    public $resource;
    public $_customer;
    public $customerFactory; 
    public $_customerGroup;
    public $_customerGroupColl;  
    public $sellersCollectionFactory;
    public $storeManager;
    public $_productMetadataInterface;
    public $productCollectionFactory;
    public $dataHelper;

    public function __construct(
        \Purpletree\Marketplace\Model\ResourceModel\Seller\CollectionFactory $sellersCollectionFactory,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $customerFactory,
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupColl,
		 \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup,    
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\App\ProductMetadataInterface $productMetadataInterface,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
        \Magento\Customer\Model\Customer $customers,
		\Purpletree\Marketplace\Helper\Data $dataHelper,
        array $data = []
    ) {
        $this->resourcees   			  = $resource;
        $this->_customer = $customers;
		 $this->customerFactory = $customerFactory; 
		  $this->_customerGroup = $customerGroup;
		 $this->_customerGroupColl = $customerGroupColl;  
        $this->sellersCollectionFactory   = $sellersCollectionFactory;
        $this->storeManager               = $storeManager;
        $this->_productMetadataInterface  = $productMetadataInterface;
        $this->productCollectionFactory   = $productCollectionFactory;
		$this->dataHelper               =       $dataHelper;
        parent::__construct($context, $data);
    }
    public function getVersion()
    {
        return $this->_productMetadataInterface->getVersion();
    }
    public function getsellersearchpost()
    {
        $searchseller = '';
        if ($this->getRequest()->getParam('searchseller') && $this->getRequest()->getParam('searchseller') != '') {
            $searchseller = $this->getRequest()->getParam('searchseller');
        }
        //return $searchseller;
    }
	public function getsellersearchpostgroup()
    {
        $searchsellers = '';
        if ($this->getRequest()->getParam('searchsellers') && $this->getRequest()->getParam('searchsellers') != '') {
            $searchsellers = $this->getRequest()->getParam('searchsellers');
        }
        //return $searchseller;
    }
    public function getAllStores()
    {    
	     
						   
						    $this->customerGroups = $this->_customerGroupColl->create()->toOptionArray();
							
							$group_id ="";
							if($this->getRequest()->getParam('searchseller')){
							$group_id =  $this->getRequest()->getParam('searchseller');
							}
							
							if(!empty($this->customerGroups)){
								foreach($this->customerGroups as $key=>$val){
									if(strtolower($val['label'])==strtolower($group_id)){
										$group_id=$val['value'];
									}	
								}
							}
						
							
        if (!$this->sellers) {
            if ($this->getRequest()->getParam('searchseller') && $this->getRequest()->getParam('searchseller') != '') {
                $this->sellers = $this->sellersCollectionFactory->create()
                           ->addFieldToSelect(
                               '*'
                           )->addFieldToFilter(['main_table.store_name','group_id'], [['like' => '%' . $this->getRequest()->getParam('searchseller'). '%'],[$group_id]])
                                    ->addFieldToFilter(
                                        'main_table.status_id',
                                        '1'
                                    )->setOrder(
                                        'main_table.created_at',
                                        'desc'
                                    );
            }else if($this->getRequest()->getParam('searchsellers') && $this->getRequest()->getParam('searchsellers') != ''){
				$this->sellers = $this->sellersCollectionFactory->create()
                           ->addFieldToSelect(
                               '*'
                           )->addFieldToFilter('main_table.store_name', ['like' => '%' . $this->getRequest()->getParam('searchsellers'). '%'])
                                    ->addFieldToFilter(
                                        'main_table.status_id',
                                        '1'
                                    )->setOrder(
                                        'main_table.created_at',
                                        'desc'
                                    );
				
			}else {
                    $this->sellers = $this->sellersCollectionFactory->create()
                           ->addFieldToSelect(
                               '*'
                           )->addFieldToFilter(
                               'main_table.status_id',
                               '1'
                           )->setOrder(
                               'main_table.created_at',
                               'desc'
                           );
            }

			$second_table_name = $this->resourcees->getTableName('customer_entity'); 
			$this->sellers->getSelect()->joinRight(array('second' => $second_table_name),
                                               '(main_table.seller_id = second.entity_id)');
        }
        //return $this->sellers->addFieldToFilter('group_id', '1');
		return $this->sellers;
    }
        /**
         * @return $this
         */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getAllStores()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getAllStores()
            );
            $this->setChild('pager', $pager);
            $this->getAllStores()->load();
        }
        return $this;
    }
        /**
         * @return string
         */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }
    public function getImageUrl()
    {
        //return $this->storeManager->getStore()->getBaseUrl().'pub/media/';
		return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }
    public function getSellerProducts($sellerId)
    {
            $this->productCollection = $this->productCollectionFactory->create()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', 4)
                ->addAttributeToFilter('status', 1)
				->addStoreFilter($this->_storeManager->getStore()->getId())
                ->addAttributeToFilter('seller_id', $sellerId)
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
        return $this->productCollection;
    }
	
	public function getCustomerGroups() {
			$customerGroups = $this->_customerGroup->toOptionArray();
			return $customerGroups;
}
	public function getCustomersCollection() {
        return $this->_customer->getCollection()
            ->addAttributeToSelect("*")
            ->load();
    }
	public function getGroupFilter()
	{
		return $groupEnable=$this->dataHelper->getGeneralConfig('seller_group_filter/seller_group_enabled');
	}
}
