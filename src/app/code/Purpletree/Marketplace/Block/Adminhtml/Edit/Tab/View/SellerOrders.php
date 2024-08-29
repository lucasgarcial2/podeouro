<?php

/**
 * Purpletree_Marketplace SellerProducts
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
class SellerOrders extends \Magento\Backend\Block\Widget\Grid\Extended
{
    protected $orderCollectionFactory;
    
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $data
     */
    public $_coreRegistry;
    public $_sellerorderCollectionFactory;
    public $_orderConfig;
     
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory $sellerorderCollectionFactory,
        array $data = []
    ) {
        $this->_coreRegistry                     = $coreRegistry;
        $this->_sellerorderCollectionFactory     = $sellerorderCollectionFactory;
        $this->_orderConfig                      = $orderConfig;
        $this->orderCollectionFactory            = $orderCollectionFactory;
         
        parent::__construct($context, $backendHelper, $data);
    }
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }
    /**
     * Initialize the orders grid.
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
         $this->setId('seller_orders_grid');
        $this->setDefaultSort('created_at', 'desc');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
                $collectiossn = $this->_sellerorderCollectionFactory->create();
                $sellerId   = $this->getSellerId();
				 $orderids = array();
        foreach ($collectiossn as $dddd) {
            if ($sellerId == $dddd->getSellerId()) {
                $orderids[] = $dddd->getOrderId();
            }
        }
        $collection = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
            '*'
        )->addFieldToFilter(
            'status',
            ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
        )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
            'created_at',
            'desc'
        );
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn('increment_id', ['header' => __('Order'), 'width' => '100', 'index' => 'increment_id']);

        $this->addColumn(
            'created_at',
            ['header' => __('Purchased'), 'index' => 'created_at', 'type' => 'datetime']
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $this->addColumn(
                'store_id',
                ['header' => __('Purchase Point'), 'index' => 'store_id', 'type' => 'store', 'store_view' => true]
            );
        }

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
