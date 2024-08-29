<?php

/**
 * Purpletree_Marketplace seller
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
 
namespace Purpletree\Marketplace\Block\Adminhtml;

class Seller extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
   public $_controller;
   public $_blockGroup;
   public $_headerText;
   public $_addButtonLabel;
    
    protected function _construct()
    {
        $this->_controller = 'adminhtml_seller';
        $this->_blockGroup = 'Purpletree_Marketplace';
        $this->_headerText = __('Sellers');
        $this->_addButtonLabel = __('Create New Seller');
        parent::_construct();
    }
}
