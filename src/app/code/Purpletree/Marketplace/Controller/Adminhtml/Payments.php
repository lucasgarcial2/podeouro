<?php
/**
 * Purpletree_Marketplace Payments
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

abstract class Payments extends \Magento\Backend\App\Action
{
    /**
     * constructor
     *
     * @param \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public $_paymentsFactory;
    public $_coreRegistry;

    public function __construct(
        \Purpletree\Marketplace\Model\PaymentsFactory $paymentsFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->_paymentsFactory     = $paymentsFactory;
        $this->_coreRegistry          = $coreRegistry;
        parent::__construct($context);
    }
    
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Purpletree_Marketplace::payments');
    }
    
    /**
     * Init Payments
     *
     * @return \Purpletree\Marketplace\Model\Payments
     */
    protected function _initPayments()
    {
        $paymentsId  = (int) $this->getRequest()->getParam('entity_id');
        $payments    = $this->_paymentsFactory->create();
        if ($paymentsId) {
            $payments->load($paymentsId);
        }
        $this->_coreRegistry->register('purpletree_marketplace_payments', $payments);
        return $payments;
    }
}
