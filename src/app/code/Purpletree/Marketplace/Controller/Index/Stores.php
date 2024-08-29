<?php
/**
 * Purpletree_Marketplace Stores
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

class Stores extends Action
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Action\Context
     * @param \Magento\Customer\Model\Session
     * @param \Magento\Store\Model\StoreManagerInterface
     * @param \Magento\Framework\Registry
     * @param \Purpletree\Marketplace\Model\ResourceModel\Seller
     * @param \Magento\Framework\Controller\Result\ForwardFactory
     * @param \Purpletree\Marketplace\Helper\Data
     * @param \Magento\Framework\View\Result\PageFactory
     *
     */
    public $resultPageFactory;
    public $resultForwardFactory;
    public $dataHelper;
    public $_resultPage;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Purpletree\Marketplace\Helper\Data $dataHelper,
        \Purpletree\Marketplace\Helper\Processdata $processdata,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
    
        $this->resultPageFactory        =       $resultPageFactory;
        $this->resultForwardFactory     =       $resultForwardFactory;
        $this->dataHelper               =       $dataHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        $moduleEnable=$this->dataHelper->getGeneralConfig('general/enabled');
        if (!$moduleEnable) {
                $resultForward = $this->resultForwardFactory->create();
                return $resultForward->forward('noroute');
        }
              
        $this->_resultPage = $this->resultPageFactory->create();
        $this->_resultPage->getConfig()->getTitle()->set(__('All Stores'));
         
        return $this->_resultPage;
    }
}
