<?php
/**
 * Purpletree_Marketplace SellerCommission
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
namespace Purpletree\Marketplace\Block\Adminhtml\Edit\Tab\View;
 
use Magento\Customer\Controller\RegistryConstants;
 
/**
 * Adminhtml customer recent orders grid block
 */
class SellerReviews extends \Magento\Backend\Block\Widget\Grid\Extended
{
      /**
       * Constructor
       *
       * @param \Magento\Backend\Block\Template\Context $context
       * @param \Magento\Backend\Helper\Data $backendHelper
       * @param \Purpletree\Marketplace\Model\ResourceModel\Reviews\CollectionFactory $collectionFactory,
       * @param \Magento\Framework\Registry $coreRegistry
       * @param array $data
       */
      public $_coreRegistry;
      public $reviewsCollectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Purpletree\Marketplace\Model\ReviewsFactory $reviewsCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->reviewsCollectionFactory     =       $reviewsCollectionFactory;
        parent::__construct($context, $backendHelper, $data);
    }
 
    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('seller_reviews_ids');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
    }
    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
            $collection = $this->reviewsCollectionFactory->create();
            $this->reviews = $collection->getCollection()
                            ->addFieldToFilter('seller_id', $this->getSellerId());
        
        return $this->reviews;
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_getCollection());
        return parent::_prepareCollection();
    }
    
     /**
      * add Column Filter To Collection
      */
    protected function _addColumnFilterToCollection($column)
    {
        parent::_addColumnFilterToCollection($column);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'index' => 'entity_id',
                'width' => '100px'
            ]
        );
        $this->addColumn(
            'customer_id',
            [
              'header' => __('Customer Name'),
              'index' => 'customer_id',
              'renderer'  => 'Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer\GetCustomer'
            ]
        );
        $this->addColumn(
            'rating',
            [
              'header' => __('Rating'),
              'index' => 'rating'
            ]
        );
        $this->addColumn(
            'review_title',
            [
              'header' => __('Review Title'),
              'index' => 'review_title'
            ]
        );
        $this->addColumn(
            'review_description ',
            [
              'header' => __('Review Description '),
              'index' => 'review_description',
            ]
        );
        $this->addColumn(
            'status ',
            [
              'header' => __('Review Status '),
              'index' => 'status',
               'renderer'  => 'Purpletree\Marketplace\Block\Adminhtml\Reviews\Renderer\Status'
            ]
        );
        $this->addColumn(
            'created_at',
            [
              'header' => __('Date'),
              'index' => 'created_at',
            ]
        );
       
        return parent::_prepareColumns();
    }
 
    /**
     * Get headers visibility
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHeadersVisibility()
    {
        return $this->getCollection()->getSize() >= 0;
    }
    public function getSellerId()
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }
}
