<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://venustheme.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Autosearch
 * @copyright  Copyright (c) 2016 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Autosearch\Model;
use Magento\Search\Model\QueryFactory;

class Search extends \Magento\Framework\DataObject
{

    /**
     * Product collection factory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_reportCollection;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $_catalogProductVisibility;


    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * \Magento\Framework\App\ResourceConnection
     * @var [type]
     */
    protected $_resource;

    /**
     *  @var \Ves\Autosearch\Model\ResourceModel\Search\CollectionFactory
     */
    protected $_autoSearchCollection;

    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    /** 
     * Query factory
     *
     * @var QueryFactory
     */
    protected $queryFactory;
    /**
     * Page cache tag
     */
    const CACHE_TAG        = 'CACHE_AUTOSEARCH_TAG';
    const CACHE_WIDGET_TAG = 'VES_AUTOSEARCH_WIDGET';

    /**
     * @param \Magento\Framework\Model\Context                          $context                  
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory 
     * @param \Magento\Store\Model\StoreManagerInterface                $storeManager             
     * @param \Magento\Framework\UrlInterface                           $url                                    
     * @param array                                                     $data                     
     */

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $reportCollection,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Ves\Autosearch\Model\ResourceModel\Search\CollectionFactory $autoSearchCollection,
        QueryFactory $queryFactory,
        array $data = []
        ) {
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_reportCollection = $reportCollection;
        $this->_catalogProductVisibility = $catalogProductVisibility;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_autoSearchCollection = $autoSearchCollection;
        $this->queryFactory = $queryFactory;
        parent::__construct($data);
        
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ves\autosearch\Model\ResourceModel\Search');
    }


    public function getResultSearchCollection($searchstring, $category = [], $storeid) {
        $collection = $this->_autoSearchCollection->create();
        $collection->setVisibility($this->_catalogProductVisibility->getVisibleInCatalogIds());
        $collection->addAttributeToSelect('*')
        ->addSearchFilter($searchstring)
        ->addMinimalPrice() 
        ->addUrlRewrite()
        ->addTaxPercents()
        ->addStoreFilter($storeid);
        $collection->addCategoryFilter($category);    
        $collection->getSelect()->order('rand()');

        // echo $collection->getSelect();
        return $collection;
    }
    private function getSuggestCollection()
    {
        return $this->queryFactory->get()->getSuggestCollection();
    }
    public function getItems()
    {
        $collection = $this->getSuggestCollection();
        $query = $this->queryFactory->get()->getQueryText();
        $result = [];
        foreach ($collection as $item) {
            $resultItem = $this->itemFactory->create([
                'title' => $item->getQueryText(),
                'num_results' => $item->getNumResults(),
                ]);
            if ($resultItem->getTitle() == $query) {
                array_unshift($result, $resultItem);
            } else {
                $result[] = $resultItem;
            }
        }
        return $result;
    }


}
