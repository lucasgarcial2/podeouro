<?php
/**
 * Purpletree_Marketplace SellerPayments
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
class SellerPayments extends \Magento\Backend\Block\Widget\Grid\Extended
{
     /**
      * Constructor
      *
      * @param \Magento\Backend\Block\Template\Context $context
      * @param \Magento\Backend\Helper\Data $backendHelper
      * @param \Purpletree\Marketplace\Model\ResourceModel\Payments\CollectionFactory $collectionFactory
      * @param \Magento\Framework\Registry $coreRegistry
      * @param array $data
      */
      public $_coreRegistry;
      public $_collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Purpletree\Marketplace\Model\ResourceModel\Payments\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_collectionFactory = $collectionFactory;
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
        $this->setId('seller_payments_ids');
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
        $collection = $this->_collectionFactory->create()->addFieldToFilter('seller_id', $this->getSellerId());
        return $collection;
    }

    protected function _prepareCollection()
    {
        $this->setCollection($this->_getCollection());
        return parent::_prepareCollection();
    }
    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();//get the parent class buttons
        $addButton = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
        ->setData([
        'label'     => __("Create Payment"),
        'onclick'   => "setLocation('".$this->getUrl('purpletree_marketplace/payments/new', ['_current' => true])."')",
        'class'   => 'task'
        ])->toHtml();
        return $addButton.$html;
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
              'header' => __('Entity Idd'),
              'index' => 'entity_id',
              'width' => '100px'
            ]
        );
        $this->addColumn(
            'transaction_id',
            [
              'header' => __('Transaction Id'),
              'index' => 'transaction_id',
            ]
        );
        $this->addColumn(
            'amount',
            [
              'header' => __('Amount'),
              'index' => 'amount',
              'renderer'  => 'Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer\Currency'
            ]
        );
        $this->addColumn(
            'status',
            [
              'header' => __('Status'),
              'index' => 'status',
            ]
        );
        $this->addColumn(
            'payment_mode',
            [
              'header' => __('Payment Mode'),
              'index' => 'payment_mode',
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
