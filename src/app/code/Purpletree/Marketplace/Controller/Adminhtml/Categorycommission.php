<?php
/**
 * Purpletree_Marketplace Categorycommission
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Purpletree License that is bundled with this package in the file license.txt.
 * It is also available through online at this URL: https://www.purpletreesoftware.com/license.html
 *
 * @category    Purpletree
 * @package     Purpletree_Marketplace
 * @author      Purpletree Infotech Private Limited
 * @copyright   Copyright (c) 2017
 * @license     https://www.purpletreesoftware.com/license.html
 */
 
namespace Purpletree\Marketplace\Controller\Adminhtml;

abstract class Categorycommission extends \Magento\Backend\App\Action
{
    /**
     * Categorycommission Factory
     *
     * @var \Purpletree\Marketplace\Model\CategorycommissionFactory
     */
    protected $_categorycommissionFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * constructor
     *
     * @param \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Purpletree\Marketplace\Model\CategorycommissionFactory $categorycommissionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_categorycommissionFactory     = $categorycommissionFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::commission');
    }
    
    /**
     * Init Categorycommission
     *
     * @return \Purpletree\Marketplace\Model\Categorycommission
     */
    protected function _initCategorycommission()
    {
        $categorycommissionId  = (int) $this->getRequest()->getParam('entity_id');
        $categorycommission    = $this->_categorycommissionFactory->create();
        if ($categorycommissionId) {
            $categorycommission->load($categorycommissionId);
        }
        $this->_coreRegistry->register('purpletree_marketplace_categorycommission', $categorycommission);
        return $categorycommission;
    }
}
