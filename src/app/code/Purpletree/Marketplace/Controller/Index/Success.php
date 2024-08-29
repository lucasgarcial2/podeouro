<?php
/**
 * Purpletree_Marketplace Index
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

namespace Purpletree\Marketplace\Controller\Index;

use \Magento\Framework\App\Action\Action;

class Success extends Action
{
    public $_session;
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Session\Generic $session
    ) {
        $this->_session                     =       $session;
         parent::__construct($context);
    }
    /**
     * Multishipping checkout success page
     *
     * @return void
     */
    public function execute()
    {
        $ids = $this->_session->getOrderIds();
        if (!isset($ids) || empty($ids)) {
            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        } else {
            $this->_view->loadLayout();
            $this->_view->renderLayout();
        }
    }
}
