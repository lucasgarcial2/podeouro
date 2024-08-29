<?php
/**
 * Purpletree_Marketplace Commission
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

abstract class Commission extends \Magento\Backend\App\Action
{
     /**
      * constructor
      *
      * @param \Purpletree\Marketplace\Model\CommissionFactory $commissionFactory
      * @param \Magento\Framework\Registry $coreRegistry
      * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
      * @param \Magento\Backend\App\Action\Context $context
      */

      public $_commissionFactory;
      public $_coreRegistry;

    public function __construct(
        \Purpletree\Marketplace\Model\CommissionFactory $commissionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->_commissionFactory     = $commissionFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init Commission
     *
     * @return \Purpletree\Marketplace\Model\Commission
     */
    protected function _initCommission()
    {
        $commissionId  = (int) $this->getRequest()->getParam('commission_id');
        /** @var \Purpletree\Marketplace\Model\Commission $commission */
        $commission    = $this->_commissionFactory->create();
        if ($commissionId) {
            $commission->load($commissionId);
        }
        $this->_coreRegistry->register('purpletree_marketplace_commission', $commission);
        return $commission;
    }
}
