<?php
namespace Purpletree\Marketplace\Model\ResourceModel\Sellerorderinvoice;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'purpletree_marketplace_sellerorderinvoice_collection';
    protected $_eventObject = 'sellerorderinvoice_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Purpletree\Marketplace\Model\Sellerorderinvoice', 'Purpletree\Marketplace\Model\ResourceModel\Sellerorderinvoice');
    }
}
