<?php
namespace Purpletree\Marketplace\Model\ResourceModel;

class Sellerorder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
    
        parent::__construct($context);
    }
    
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_sellerorder', 'entity_id');
    }
    public function getSellerStatus($sellerId, $orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
        ->from($this->getMainTable(), 'order_status')
        ->where('seller_id = ?', $sellerId)
        ->where('order_id = ?', $orderId);
        return $adapter->fetchRow($select);
    }
    public function getSellerProducts($sellerId, $orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable())
             ->where('seller_id = ?', $sellerId)
             ->where('order_id = ?', $orderId);
        return $adapter->fetchAll($select);
    }
    public function getEntityIdfromOrderId($sellerId, $orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'entity_id')
             ->where('seller_id = ?', $sellerId)
             ->where('order_id = ?', $orderId);
        return $adapter->fetchAll($select);
    }
	    public function getTotalShipping($sellerId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'shipping')
            ->where('seller_id = ?', $sellerId)
			->group(['seller_id', 'shipping']);
        return $adapter->fetchAll($select);
    }
}
