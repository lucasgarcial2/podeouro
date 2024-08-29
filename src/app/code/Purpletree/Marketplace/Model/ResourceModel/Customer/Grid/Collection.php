<?php

/**
 * Purpletree_Marketplace Collection
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

namespace Purpletree\Marketplace\Model\ResourceModel\Customer\Grid;

use Magento\Customer\Model\ResourceModel\Customer;
use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Customer\Model\ResourceModel\Grid\Collection
{

    protected function _initSelect()
    { 
        $this->getSelect()->joinLeft(
            ['secondTable' => $this->getTable('purpletree_marketplace_stores')],
            'main_table.entity_id = secondTable.seller_id',
            ['seller_id','store_name','store_email','status_id as seller_status_id','created_at as seller_created_at']
        );
        $this->addFilterToMap('store_name', 'secondTable.store_name');
        $this->addFilterToMap('seller_created_at', 'secondTable.created_at');
        $this->addFilterToMap('seller_updated_at', 'secondTable.updated_at');
        parent::_initSelect();
        return $this;
    }
}
