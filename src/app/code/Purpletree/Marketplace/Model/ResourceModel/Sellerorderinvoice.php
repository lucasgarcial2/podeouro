<?php
namespace Purpletree\Marketplace\Model\ResourceModel;

class Sellerorderinvoice extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }
    
    protected function _construct()
    {
        $this->_init('purpletree_marketplace_sellerorderinvoice', 'entity_id');
    }
    public function getSellerOrderInvoice($sellerId, $orderId)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
        ->from($this->getMainTable())
        ->where('seller_id = ?', $sellerId)
        ->where('order_id = ?', $orderId);
        return $adapter->fetchRow($select);
    }
}
