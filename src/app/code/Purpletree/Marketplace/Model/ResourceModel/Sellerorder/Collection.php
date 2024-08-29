<?php
namespace Purpletree\Marketplace\Model\ResourceModel\Sellerorder;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'purpletree_marketplace_sellerorder_collection';
    protected $_eventObject = 'sellerorder_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Marketplace\Model\Sellerorder', 'Purpletree\Marketplace\Model\ResourceModel\Sellerorder');
    }
}
