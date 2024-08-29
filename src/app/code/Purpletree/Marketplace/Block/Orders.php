<?php
/**
 * Purpletree_Marketplace Orders
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

use \Magento\Framework\App\ObjectManager;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;

/**
 * Sales order history block
 */
class Orders extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Sales\Model\ResourceModel\Order\Collection */
    
    protected $orders;
    
    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param array $data
     */

     public $orderCollectionFactory;
     public $_orderConfig;
     public $coreRegistry;
     public $_sellerorderCollectionFactory;
     public $_sellerOrder;
     public $statusCollectionFactory;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder $sellerOrder,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Purpletree\Marketplace\Model\ResourceModel\Sellerorder\CollectionFactory $sellerorderCollectionFactory,
        array $data = []
    ) {
        $this->orderCollectionFactory           = $orderCollectionFactory;
        $this->_orderConfig                     = $orderConfig;
        $this->coreRegistry             =       $coreRegistry;
        $this->_sellerorderCollectionFactory    = $sellerorderCollectionFactory;
        $this->_sellerOrder                     = $sellerOrder;
        $this->statusCollectionFactory = $statusCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('marketplace/index/orderview', ['order_id' => $order->getId()]);
    }
    
    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
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
    
    /**
     * @return CollectionFactoryInterface
     *
     * @deprecated
     */
    private function getOrderCollectionFactory()
    {
        if ($this->orderCollectionFactory === null) {
            $this->orderCollectionFactory = ObjectManager::getInstance()->get(CollectionFactoryInterface::class);
        }
        return $this->orderCollectionFactory;
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        $collectiossn = $this->_sellerorderCollectionFactory->create();
                $sellerId   = (int) $this->coreRegistry->registry('sellerId');
        foreach ($collectiossn as $dddd) {
            if ($sellerId == $dddd->getSellerId()) {
                $orderids[] = $dddd->getOrderId();
            }
        }
    
        if (!$this->orders) {
            if ($this->getRequest()->isAjax()) {
                $data = $this->getRequest()->getPostValue();
				$fromDate  =  (isset($data['from']) && $data['from']!='')?date('Y-m-d', strtotime($data['from'])).' 00:00:00':'';
				$toDate  = (isset($data['report_to']) && $data['report_to']!='')?date('Y-m-d', strtotime($data['report_to'])).' 23:59:59':'';
                $this->orders = $this->getOrdersAjax($fromDate, $toDate, $orderids);
            } else {
                if (!empty($orderids)) {
                    $this->orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                        '*'
                    )->addFieldToFilter(
                        'status',
                        ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
                    )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                        'created_at',
                        'desc'
                    );
                }
            }
        }
        return $this->orders;
    }
    public function getSellerOrderStatus($order_id)
    {
        $sellerId   =   (int) $this->coreRegistry->registry('sellerId');
        $ddd        = $this->_sellerOrder->getSellerStatus($sellerId, $order_id);
        return $this->getStatusOptions($ddd['order_status']);
    }
    public function getStatusOptions($code)
    {
        $options = $this->statusCollectionFactory->create()->toOptionArray();
        foreach ($options as $status) {
            if ($code == $status['value']) {
                return $status['label'];
            }
        }
    }
    public function getOrdersAjax($fromDate, $toDate, $orderids = [])
    {
        if ($fromDate =='' && $toDate =='') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } elseif ($fromDate == '') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                'lt'=>$toDate
                ]
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } elseif ($toDate == '') {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'created_at',
                [
                'gt'=>$fromDate
                ]
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        } else {
            $orders = $this->getOrderCollectionFactory()->create()->addFieldToSelect(
                '*'
            )->addAttributeToFilter(
                'created_at',
                [
                    'from'=>$fromDate,
                    'to'=>$toDate
                 ]
            )->addFieldToFilter(
                'status',
                ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
            )->addAttributeToFilter('entity_id', ['in' => $orderids])->setOrder(
                'created_at',
                'desc'
            );
        }
        return $orders;
    }
}
