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
class SellerProducts extends \Magento\Backend\Block\Widget\Grid\Extended
{
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
     public $_collectionFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
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
        $this->setId('seller_products_ids');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setFilterVisibility(true);
        $this->setUseAjax(true);
        if ($this->getRequest()->getParam('id')) {
            $this->setDefaultFilter(['in_product' => 1]);
        }
    }
 
    /**
     * {@inheritdoc}
     */
    protected function _getCollection()
    {
        $collection = $this->_collectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addAttributeToFilter(
            [
                ['attribute'=> 'is_seller_product','null' => true],
                ['attribute'=> 'is_seller_product','eq' => ''],
                ['attribute'=> 'is_seller_product','eq' => 0]
            ],
            '',
            'left'
        );
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
        'label'     => __("Assign Selected Products"),
        'onclick'   => "var obj = [];
                            jQuery('.checkbox.admin__control-checkbox:checked').each(function(ind,ite){
                            obj.push(jQuery(ite).attr('id'));
                            });
                             jQuery.ajax({
								url: '". $this->getUrl('purpletree_marketplace/index/savesellerproducts')."',
								data: {products:obj,seller:".$this->getSellerId().",unassign:1},
                                type: 'post',
                                dataType: 'json',
                               showLoader:true,
                               success: function(data){
                                   if(data.success == 'true') {
                                       alert('Products Assigned Successfully.');
                                       jQuery('.action-reset').trigger('click');
                                   }
                             }
                             })
							",
        'class'   => 'task'
        ])->toHtml();
        return $addButton.$html;
    }
    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'in_product',
            [
              'header_css_class' => 'a-center',
              'type' => 'checkbox',
              'name' => 'in_product',
              'align' => 'center',
              'index' => 'entity_id',
              'field_name' => 'selectedproducts[]'
            ]
        );
        $this->addColumn(
            'sku',
            [
              'header' => __('SKU'),
              'index' => 'sku',
              'width' => '100px'
            ]
        );
        $this->addColumn(
            'product_name',
            [
              'header' => __('Product Name'),
              'index' => 'name',
            ]
        );
        
        $this->addColumn(
            'product_price',
            [
              'header' => __('Product Price'),
              'index' => 'product_price',
              'renderer'  => 'Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer\Currency'
            ]
        );
        $this->addColumn(
            'status',
            [
              'header' => __('Status'),
              'index' => 'status',
              'renderer'  => 'Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer\Status'
            ]
        );
        $this->addColumn(
            'action',
            [
              'header' => __('Action'),
              'width' => '100',
              'type' => 'action',
              'getter'    => 'getId',
              'actions' => [
                  [
                      'caption' => __('View'),
                      'url' => ['base' => 'catalog/product/edit',],
                      'field' => 'id',
                      'target'    => '_blank'
                  ]
              ],
              'filter' => false,
              'sortable' => false,
              'index' => 'stores',
              'is_system' => true,
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
