<?php

/**
 * Purpletree_Marketplace GetCustomer
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

namespace Purpletree\Marketplace\Block\Adminhtml\Seller\Renderer;
 
use Magento\Framework\DataObject;
 
class GetCustomer extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public $customer;
    public function __construct(
        \Magento\Customer\Model\Customer $customer
    ) {
        $this->customer = $customer;
    }
 
    /**
     * get category name
     * @param  DataObject $row
     * @return string
     */
    public function render(DataObject $row)
    {
         $id = $row->getSellerId();
        return $this->customer->load($id)->getName();
    }
}
